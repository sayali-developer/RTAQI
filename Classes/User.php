<?php

namespace RTAQI\Classes;

use PDOException;
use PHPMailer\PHPMailer\Exception;
use RTAQI\Exceptions\InvalidInputException;
use RTAQI\Framework\Classes\Db;
use RTAQI\Exceptions\UserAuthenticationFailedException;
use RTAQI\Exceptions\UserNotActiveException;
use RTAQI\Framework\Classes\Mailer;
use Ramsey\Uuid\Guid\Guid;

class User
{
    private Db $db;
    private $user;
    private string $email;
    private int $userId;
    private string $fullName;
    private int $account_status;

    private string | null $password;

    public function __construct()
    {
        $this->db = new Db();
    }

// Method to create a user

    /**
     * @throws UserAuthenticationFailedException
     * @throws \Exception
     */
    public function createUser($email, $fullName)
    {
        // Insert into the database without a password
        $con = $this->db->connect();

        $stmt = $con->prepare("INSERT INTO users (email, fullname, is_enabled) VALUES (?, ?, ?)");

        try {
            $stmt->execute([$email, $fullName, 0]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new UserAuthenticationFailedException("User already exists with ".$email);
            }
        }
        // Fetch the user data after insertion
        $user = $this->getUser($email);

        // Generate a reset link and send the email
        $this->sendPasswordResetEmail($user['email']);

        return $user;
    }

    // Method to retrieve a user by email

    /**
     * @throws InvalidInputException
     */
    public function getUser($email)
    {
        $stmt = $this->db->connect()->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() != 1) {
            throw new InvalidInputException("User not found.");
        }
        $user = $stmt->fetch();

        $this->user = $user;
        $this->email = $email;
        $this->fullName = $user['fullname'];
        $this->userId = $user['user_id'];
        $this->account_status = $user['is_enabled'];
        $this->password = $user['password'];
        return $user;
    }
    private function hashPassword($password) : string
    {
        return hash(PASS_HASH, PASS_SALT . $password);
    }
    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getFullName(): string {
        return $this->fullName;
    }

// Method to log in a user

    /**
     * @throws UserNotActiveException
     * @throws UserAuthenticationFailedException|InvalidInputException
     */
    public function loginUser($email, $password)
    {
        $user = $this->getUser($email);

// Verify password with stored hash and salt
        $hashedPassword = $this->hashPassword($password);

        if ($hashedPassword !== $user['password']) {
            throw new UserAuthenticationFailedException();
        }

// Check if the user is active
        if (!$user['is_enabled']) {
            throw new UserNotActiveException($this);
        }

// Start the session and set user data
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['full_name'] = $user['fullname'];
        $_SESSION["hashed_password"] = $user['password'];
        return $user;
    }

// Method to log out the user
    public function logout(): void
    {
        session_unset();
        session_destroy();
        session_start();
        $this->user = [];
        $this->email = "";
        $this->fullName = "";
        $this->userId = 0;
        $this->account_status = 0;

    }

    /**
     * @throws InvalidInputException
     */
    public function getResetCodeDetails($resetCode) {
        $con = $this->db->connect();
        $stmt = $con->prepare("SELECT users.user_id, users.email, password_reset_link.reset_code, password_reset_link.created_on FROM password_reset_link join users ON password_reset_link.user_id = users.user_id WHERE password_reset_link.used = 0 AND password_reset_link.reset_code = ?;");
        $stmt->execute([$resetCode]);
        if ($stmt->rowCount() == 0) {
            throw new InvalidInputException("Invalid or already used reset link.");
        }
        $resetCodeDetails = $stmt->fetch();
        $createdOn = strtotime($resetCodeDetails['created_on']);
        $currentTime = time();
        if (($currentTime - $createdOn) > 3 * 60 * 60) { // 3 hours in seconds
            throw new InvalidInputException("Password reset link has expired, Please generate new link.");
        }
        return $resetCodeDetails;
    }
// Method to reset password

    /**
     * @throws InvalidInputException
     */
    public function resetPassword($resetCode, $newPassword): bool
    {
        $resetCodeDetails = $this->getResetCodeDetails($resetCode);
        // Now update the password
        $hashedPassword = $this->hashPassword($newPassword);

        // Get the user associated with this reset code
        $userId = $resetCodeDetails['user_id'];

        // Update the user's password and mark the reset link as used
        $con = $this->db->connect();
        $stmt = $con->prepare("
            UPDATE users 
            SET password = ?, is_enabled = 1 
            WHERE user_id = ?
        ");
        $stmt->execute([$hashedPassword, $userId]);
        $con = $this->db->connect();
        // Mark the reset link as used
        $stmt = $con->prepare("
            UPDATE password_reset_link 
            SET used = 1 
            WHERE reset_code = ?
        ");
        $stmt->execute([$resetCode]);

        return true;
    }


// Method to send password reset email

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function sendPasswordResetEmail($email = null): void
    {
        if (!is_null($email)) {
            try {
                $this->getUser($email);
            } catch (InvalidInputException) {
                throw new UserAuthenticationFailedException($email ." does not exist.");
            }
        }
        $email = $this->user['email'];
        $userId = $this->user['user_id'];

// Generate reset code (UUID) for the reset link
        $resetCode = Guid::uuid4()->toString();

// Store the reset code and its expiration
        $stmt = $this->db->connect()->prepare("INSERT INTO password_reset_link (user_id, reset_code, created_on, used) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $resetCode, date('Y-m-d H:i:s'), 0]);

// Send the email with the reset link using the Mailer class
        $mailer = new Mailer();
        $mail = $mailer->getPhpmailer();

// Set up the reset link (with expiration handling)
        $resetLink = APP_DOMAIN . "/reset-password/?code=" . $resetCode;

// Compose email content
        $mail->addAddress($email);
        $mail->Subject = "Reset Your Password";
        $mail->Body = "Please click the link below to reset your password:<br><a href='" . $resetLink . "'>" . $resetLink . "</a>";

// Send the email
        if (!$mail->send()) {
            throw new \Exception("Failed to send password reset email.");
        }
    }

    public function loggedIn(): bool
    {
        if (isset($_SESSION['user_id'])) {
            try {


                $this->getUser($_SESSION['email']);

                return $this->getAccountStatus() && $_SESSION['hashed_password'] === $this->password;
            } catch (InvalidInputException $e) {

            }
        }
        return false;
    }

    public function getAccountStatus() : bool
    {
        return $this->account_status === 1;
    }
}
