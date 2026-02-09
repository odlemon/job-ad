"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
// @ts-nocheck
const express_1 = __importDefault(require("express"));
const LifeEventsController_1 = require("../controllers/LifeEventsController");
const authenticate_1 = require("../middleware/authenticate");
const validation_1 = require("../middleware/validation");
const error_1 = require("../middleware/error");
const router = express_1.default.Router();
// Public routes (no authentication required for better accessibility)
router.get("/", (0, error_1.asyncHandler)((req, res, next) => LifeEventsController_1.lifeEventsController.getLifeEventTiles(req, res, next)));
router.get("/urgency/:urgency", (0, error_1.asyncHandler)((req, res, next) => LifeEventsController_1.lifeEventsController.getLifeEventsByUrgency(req, res, next)));
router.get("/:id", (0, validation_1.validateObjectId)('id'), (0, error_1.asyncHandler)((req, res, next) => LifeEventsController_1.lifeEventsController.getLifeEvent(req, res, next)));
// Authenticated routes
router.use(authenticate_1.authenticate);
router.post("/:id/complete", (0, validation_1.validateObjectId)('id'), (0, error_1.asyncHandler)((req, res, next) => LifeEventsController_1.lifeEventsController.markLifeEventCompleted(req, res, next)));
router.get("/user/completed", (0, error_1.asyncHandler)((req, res, next) => LifeEventsController_1.lifeEventsController.getCompletedLifeEvents(req, res, next)));
router.get("/user/recommended", (0, error_1.asyncHandler)((req, res, next) => LifeEventsController_1.lifeEventsController.getRecommendedLifeEvents(req, res, next)));
exports.default = router;
//# sourceMappingURL=lifeEventsRoutes.js.map