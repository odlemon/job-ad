"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
// @ts-nocheck
const express_1 = __importDefault(require("express"));
const BenefitsController_1 = require("../controllers/BenefitsController");
const authenticate_1 = require("../middleware/authenticate");
const validation_1 = require("../middleware/validation");
const error_1 = require("../middleware/error");
const router = express_1.default.Router();
// Public routes (no authentication required)
router.get("/", (0, error_1.asyncHandler)((req, res, next) => BenefitsController_1.benefitsController.getBenefitsLibrary(req, res, next)));
router.get("/categories", (0, error_1.asyncHandler)((req, res, next) => BenefitsController_1.benefitsController.getCategories(req, res, next)));
router.get("/:id", (0, validation_1.validateObjectId)('id'), (0, error_1.asyncHandler)((req, res, next) => BenefitsController_1.benefitsController.getBenefitsContent(req, res, next)));
// Authenticated routes
router.use(authenticate_1.authenticate);
router.post("/:id/bookmark", (0, validation_1.validateObjectId)('id'), (0, error_1.asyncHandler)((req, res, next) => BenefitsController_1.benefitsController.toggleBookmark(req, res, next)));
router.get("/user/bookmarks", (0, error_1.asyncHandler)((req, res, next) => BenefitsController_1.benefitsController.getBookmarkedContent(req, res, next)));
exports.default = router;
//# sourceMappingURL=benefitsRoutes.js.map