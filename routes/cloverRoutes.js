"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const express_1 = __importDefault(require("express"));
const CloverController_1 = require("../controllers/CloverController");
const authenticate_1 = require("../middleware/authenticate");
const error_1 = require("../middleware/error");
const rateLimiter_1 = require("../middleware/rateLimiter");
const router = express_1.default.Router();
// Apply authentication to all routes
router.use(authenticate_1.authenticate);
// Clover Chat endpoint
router.post('/chat', rateLimiter_1.cloverLimiter, // Apply rate limiting
(0, error_1.asyncHandler)(CloverController_1.cloverController.chat.bind(CloverController_1.cloverController)));
// Get chat history
router.get('/history', (0, error_1.asyncHandler)(CloverController_1.cloverController.getChatHistory.bind(CloverController_1.cloverController)));
// Get usage statistics
router.get('/usage', (0, error_1.asyncHandler)(CloverController_1.cloverController.getUsageStats.bind(CloverController_1.cloverController)));
exports.default = router;
//# sourceMappingURL=cloverRoutes.js.map