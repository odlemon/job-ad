"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
// @ts-nocheck
const express_1 = __importDefault(require("express"));
const UserController_1 = require("../controllers/UserController");
const authenticate_1 = require("../middleware/authenticate");
const validation_1 = require("../middleware/validation");
const error_1 = require("../middleware/error");
const router = express_1.default.Router();
// Protected routes - require authentication
router.use(authenticate_1.authenticate);
// User profile management
router.get("/profile", (0, error_1.asyncHandler)((req, res, next) => UserController_1.userController.getProfile(req, res, next)));
router.put("/profile", validation_1.validateProfileUpdate, (0, error_1.asyncHandler)((req, res, next) => UserController_1.userController.updateProfile(req, res, next)));
// User analytics and activity
router.get("/analytics", (0, error_1.asyncHandler)((req, res, next) => UserController_1.userController.getAnalytics(req, res, next)));
// Notification preferences
router.put("/notifications", (0, error_1.asyncHandler)((req, res, next) => UserController_1.userController.updateNotificationPreferences(req, res, next)));
// Import rate limiters
const rateLimiter_1 = require("../middleware/rateLimiter");
// Password management
router.put("/password", rateLimiter_1.passwordLimiter, (0, error_1.asyncHandler)((req, res, next) => UserController_1.userController.changePassword(req, res, next)));
// Clover AI usage statistics
router.get("/clover/usage", (0, error_1.asyncHandler)((req, res, next) => UserController_1.userController.getCloverUsage(req, res, next)));
// Account deletion
router.delete("/account", rateLimiter_1.deletionLimiter, (0, error_1.asyncHandler)((req, res, next) => UserController_1.userController.deleteAccount(req, res, next)));
exports.default = router;
//# sourceMappingURL=userRoutes.js.map