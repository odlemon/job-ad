"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const express_1 = __importDefault(require("express"));
const DashboardController_1 = require("../controllers/DashboardController");
const authenticate_1 = require("../middleware/authenticate");
const error_1 = require("../middleware/error");
const router = express_1.default.Router();
// Apply authentication to all routes
router.use(authenticate_1.authenticate);
// Get personalized dashboard
router.get('/:userId', (0, error_1.asyncHandler)(DashboardController_1.dashboardController.getPersonalizedDashboard.bind(DashboardController_1.dashboardController)));
exports.default = router;
//# sourceMappingURL=dashboardRoutes.js.map