<?php

const APP_NAME = "RTAQI";
const APP_DESCRIPTION = "Real Time Air Quality Index Application";
// DB Credentials
const DB_HOST = "localhost:3306";
const DB_USER = "<!-- DB Username -->";
const DB_PASS = "<!-- DB Password -->";
const DB_NAME = "rtaqi";

// SMTP Credentials

const SMTP_HOST = "<!-- Enter SMTP Host Here -- ?>";
const SMTP_PORT = 587;
const SMTP_USER = "<!-- SMTP Username !>";
const SMTP_PASSWORD = "<!-- SMTP Password !>";
const SMTP_DEFAULT_EMAIL = "<!-- SMTP Email !>";
const SENDER_DEFAULT_NAME = "Contact AirQuality";
const SMTP_SECURE_PROTOCOL = "tls";

// API Keys
const DATA_GOV_API_RTAQI = "<!-- data.gov.in API Key -->";

// Password Hashing

const PASS_HASH = 'sha256'; // Example, use a stronger algorithm if desired
const PASS_SALT = '<!-- Password Salt -->'; // Replace with a secure, random salt

const RESET_LINK_EXPIRATION = 3600;

// Paths & Directories
const PROJECT_ROOT = __DIR__;
const APP_PUBLIC_ROOT = __DIR__ . "/public";
const THEME_ROOT = "/assets/theme";
const APP_DOMAIN = "https://airquality.kportal.in";