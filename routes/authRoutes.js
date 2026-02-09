"use strict";
// @ts-nocheck
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const express_1 = require("express");
const AuthController_1 = require("../controllers/AuthController");
const authenticate_1 = require("../middleware/authenticate");
const jsonwebtoken_1 = __importDefault(require("jsonwebtoken"));
const passport_1 = __importDefault(require("passport"));
const logger_1 = require("../utils/logger");
const User_1 = require("../models/User");
const bcrypt_1 = __importDefault(require("bcrypt"));
const crypto_1 = __importDefault(require("crypto"));
const google_auth_library_1 = require("google-auth-library");
const apple_signin_auth_1 = __importDefault(require("apple-signin-auth"));
const JWT_SECRET = process.env.JWT_SECRET || "your_jwt_secret";
const FRONTEND_URL = process.env.FRONTEND_URL || "http://localhost:3001";
const GOOGLE_CLIENT_ID = process.env.GOOGLE_CLIENT_ID;
// Initialize Google OAuth2 client for mobile token verification
const googleClient = new google_auth_library_1.OAuth2Client(GOOGLE_CLIENT_ID);
const router = (0, express_1.Router)();
// Import validation middleware
const validation_1 = require("../middleware/validation");
const error_1 = require("../middleware/error");
const rateLimiter_1 = require("../middleware/rateLimiter");
const fileUpload_1 = require("../middleware/fileUpload");
// Basic auth routes with validation and rate limiting
router.post("/register", rateLimiter_1.authLimiter, validation_1.validateRegistration, (0, error_1.asyncHandler)(AuthController_1.authController.register.bind(AuthController_1.authController)));
router.post("/login", rateLimiter_1.authLimiter, validation_1.validateLogin, (0, error_1.asyncHandler)(AuthController_1.authController.login.bind(AuthController_1.authController)));
router.post("/logout", authenticate_1.authenticate, (0, error_1.asyncHandler)(AuthController_1.authController.logout.bind(AuthController_1.authController)));
router.get("/me", authenticate_1.authenticate, (0, error_1.asyncHandler)(AuthController_1.authController.me.bind(AuthController_1.authController)));
// Profile update with optional image upload
router.put("/profile", authenticate_1.authenticate, fileUpload_1.uploadProfileImage, validation_1.validateProfileUpdate, (0, error_1.asyncHandler)(AuthController_1.authController.updateProfile.bind(AuthController_1.authController)));
// Google OAuth routes
router.get("/google", passport_1.default.authenticate("google", {
    scope: ["profile", "email"]
}));
router.get("/google/callback", passport_1.default.authenticate("google", { failureRedirect: `${FRONTEND_URL}/login?error=oauth_failed`, session: false }), (req, res) => {
    try {
        const user = req.user;
        if (!user) {
            logger_1.logger.error("No user in Google OAuth callback");
            return res.redirect(`${FRONTEND_URL}/login?error=no_user`);
        }
        const token = jsonwebtoken_1.default.sign({
            userId: user._id,
            email: user.email,
            firstName: user.firstName,
            lastName: user.lastName,
            knowledgeLevel: user.knowledgeLevel,
        }, JWT_SECRET, { expiresIn: "7d" });
        // Redirect to frontend with token and user info
        const redirectUrl = `${FRONTEND_URL}/auth/callback?token=${token}&onboarding=${!user.onboardingCompleted}`;
        logger_1.logger.info(`Google OAuth successful for user: ${user.email}`);
        res.redirect(redirectUrl);
    }
    catch (error) {
        logger_1.logger.error("Google OAuth callback error:", error);
        res.redirect(`${FRONTEND_URL}/login?error=callback_error`);
    }
});
// Mobile-friendly Google Sign-In endpoint for Flutter
// Receives user data directly from Flutter app (no token verification needed)
router.post("/google/mobile", (0, error_1.asyncHandler)((req, res, next) => __awaiter(void 0, void 0, void 0, function* () {
    try {
        const { email, name, photoUrl, token } = req.body;
        if (!email || !name) {
            return res.status(400).json({
                success: false,
                message: "Email and name are required"
            });
        }
        // Parse name into firstName and lastName
        const nameParts = (name || "User").split(' ');
        const firstName = nameParts[0] || "User";
        const lastName = nameParts.slice(1).join(' ') || "";
        // Check if user exists with email
        let user = yield User_1.User.findOne({ email: email.toLowerCase() });
        if (!user) {
            // Create new user from Flutter data
            const randomPassword = crypto_1.default.randomBytes(16).toString("hex");
            const hashedPassword = yield bcrypt_1.default.hash(randomPassword, 12);
            user = yield User_1.User.create(Object.assign({ firstName,
                lastName, email: email.toLowerCase(), password: hashedPassword, language: 'en', knowledgeLevel: 'beginner', onboardingCompleted: false, isPremium: false, cloverQuestionsUsed: 0, cloverQuestionsLimit: 10, lastActive: new Date() }, (photoUrl && { profileImage: { url: photoUrl, uploadedAt: new Date() } })));
            logger_1.logger.info(`New user registered from Flutter: ${email}`);
        }
        else {
            // Update existing user
            user.firstName = firstName;
            user.lastName = lastName;
            user.lastActive = new Date();
            // Update profile image if provided
            if (photoUrl) {
                user.profileImage = { url: photoUrl, uploadedAt: new Date() };
            }
            yield user.save();
            logger_1.logger.info(`Existing user logged in from Flutter: ${email}`);
        }
        // Generate JWT token
        const jwtToken = jsonwebtoken_1.default.sign({
            userId: user._id,
            email: user.email,
            firstName: user.firstName,
            lastName: user.lastName,
            knowledgeLevel: user.knowledgeLevel,
        }, JWT_SECRET, { expiresIn: "7d" });
        // Return mobile-friendly response
        return res.status(200).json({
            success: true,
            message: user.onboardingCompleted ? "Logged in successfully" : "Welcome! Please complete your onboarding.",
            token: jwtToken,
            user: {
                userId: user._id,
                email: user.email,
                firstName: user.firstName,
                lastName: user.lastName,
                fullName: `${user.firstName} ${user.lastName}`,
                knowledgeLevel: user.knowledgeLevel,
                onboardingCompleted: user.onboardingCompleted,
                isPremium: user.isPremium,
                profileImage: user.profileImage || null,
                requiresOnboarding: !user.onboardingCompleted,
                isNewUser: !user.onboardingCompleted
            }
        });
    }
    catch (error) {
        logger_1.logger.error("Google mobile auth error:", error);
        next(error);
    }
})));
// Apple Sign-In endpoint for Flutter
router.post("/apple/mobile", (0, error_1.asyncHandler)((req, res, next) => __awaiter(void 0, void 0, void 0, function* () {
    try {
        const { appleIdToken, appleUser } = req.body;
        if (!appleIdToken) {
            return res.status(400).json({
                success: false,
                message: "Apple ID token is required"
            });
        }
        // Verify Apple ID token
        const appleData = yield verifyAppleIdToken(appleIdToken);
        if (!appleData || !appleData.sub) {
            return res.status(401).json({
                success: false,
                message: "Invalid Apple ID token"
            });
        }
        // Check if user exists with Apple ID
        let user = yield User_1.User.findOne({ appleId: appleData.sub });
        if (!user) {
            // For Apple, email might be hidden, so check if we have user data from first sign-in
            let userEmail = appleData.email;
            // If we have appleUser data (first time sign-in), use that email
            if (appleUser && appleUser.email) {
                userEmail = appleUser.email;
            }
            if (userEmail) {
                // Check if user exists with email (link accounts)
                user = yield User_1.User.findOne({ email: userEmail });
                if (user) {
                    // Link Apple account to existing user
                    user.appleId = appleData.sub;
                    user.lastActive = new Date();
                    yield user.save();
                    logger_1.logger.info(`Linked Apple account to existing user: ${user.email}`);
                }
            }
            if (!user) {
                // Create new user from Apple profile
                if (!userEmail) {
                    return res.status(400).json({
                        success: false,
                        message: "Email is required for account creation. Please try again and allow email access."
                    });
                }
                // Parse name from appleUser data (only available on first sign-in)
                let firstName = "User";
                let lastName = "";
                if (appleUser && appleUser.name) {
                    firstName = appleUser.name.firstName || "User";
                    lastName = appleUser.name.lastName || "";
                }
                const randomPassword = crypto_1.default.randomBytes(16).toString("hex");
                const hashedPassword = yield bcrypt_1.default.hash(randomPassword, 12);
                user = yield User_1.User.create({
                    firstName,
                    lastName,
                    email: userEmail,
                    password: hashedPassword,
                    appleId: appleData.sub,
                    language: 'en',
                    knowledgeLevel: 'beginner',
                    onboardingCompleted: false,
                    isPremium: false,
                    cloverQuestionsUsed: 0,
                    cloverQuestionsLimit: 10,
                    lastActive: new Date(),
                });
                logger_1.logger.info(`New Apple user registered: ${userEmail}`);
            }
        }
        else {
            // Update last active for existing Apple user
            user.lastActive = new Date();
            yield user.save();
            logger_1.logger.info(`Existing Apple user logged in: ${user.email}`);
        }
        // Generate JWT token
        const token = jsonwebtoken_1.default.sign({
            userId: user._id,
            email: user.email,
            firstName: user.firstName,
            lastName: user.lastName,
            knowledgeLevel: user.knowledgeLevel,
        }, JWT_SECRET, { expiresIn: "7d" });
        // Return mobile-friendly response
        return res.status(200).json({
            success: true,
            message: user.onboardingCompleted ? "Logged in successfully" : "Welcome! Please complete your onboarding.",
            token: jwtToken,
            user: {
                userId: user._id,
                email: user.email,
                firstName: user.firstName,
                lastName: user.lastName,
                fullName: `${user.firstName} ${user.lastName}`,
                knowledgeLevel: user.knowledgeLevel,
                onboardingCompleted: user.onboardingCompleted,
                isPremium: user.isPremium,
                profileImage: user.profileImage || null,
                requiresOnboarding: !user.onboardingCompleted,
                isNewUser: !user.onboardingCompleted
            }
        });
    }
    catch (error) {
        logger_1.logger.error("Apple mobile auth error:", error);
        next(error);
    }
})));
// Helper function to verify Apple ID token
function verifyAppleIdToken(idToken) {
    return __awaiter(this, void 0, void 0, function* () {
        try {
            const appleData = yield apple_signin_auth_1.default.verifyIdToken(idToken, {
                // Apple's audience is typically your app's bundle ID
                audience: process.env.APPLE_BUNDLE_ID || 'com.lysp.clearcover-health',
                ignoreExpiration: false,
            });
            return appleData;
        }
        catch (error) {
            logger_1.logger.error("Apple ID token verification failed:", error);
            throw new Error("Invalid Apple ID token");
        }
    });
}
// Note: Google OAuth verification is no longer needed since authentication happens on Flutter app side
// Apple Sign-In implemented above as /apple/mobile endpoint ✅
exports.default = router;
//# sourceMappingURL=authRoutes.js.map