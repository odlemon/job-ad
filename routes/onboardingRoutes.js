"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
// @ts-nocheck
const express_1 = __importDefault(require("express"));
const OnboardingController_1 = require("../controllers/OnboardingController");
const authenticate_1 = require("../middleware/authenticate");
const router = express_1.default.Router();
// Public route - get assessment questions (no auth required for better UX)
router.get("/questions", (req, res, next) => OnboardingController_1.onboardingController.getAssessmentQuestions(req, res, next));
// All other onboarding routes require authentication
router.use(authenticate_1.authenticate);
// Get onboarding status
router.get("/status", (req, res, next) => OnboardingController_1.onboardingController.getOnboardingStatus(req, res, next));
// Import validation and error handling
const validation_1 = require("../middleware/validation");
const error_1 = require("../middleware/error");
const rateLimiter_1 = require("../middleware/rateLimiter");
// Submit knowledge assessment
router.post("/assessment", rateLimiter_1.assessmentLimiter, validation_1.validateAssessment, (0, error_1.asyncHandler)((req, res, next) => OnboardingController_1.onboardingController.submitAssessment(req, res, next)));
// Apply assessment results and complete onboarding
router.post("/complete", (0, error_1.asyncHandler)((req, res, next) => OnboardingController_1.onboardingController.applyAssessmentResults(req, res, next)));
// Get personalized recommendations
router.get("/recommendations", (0, error_1.asyncHandler)((req, res, next) => OnboardingController_1.onboardingController.getPersonalizedRecommendations(req, res, next)));
exports.default = router;
//# sourceMappingURL=onboardingRoutes.js.map