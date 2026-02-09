"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
// @ts-nocheck
const express_1 = __importDefault(require("express"));
const PropertyController_1 = require("../controllers/PropertyController");
const authenticate_1 = require("../middleware/authenticate");
const authenticate_2 = require("../middleware/authenticate");
const router = express_1.default.Router();
// Public routes (no authentication required)
router.get("/", (req, res, next) => PropertyController_1.propertyController.getProperties(req, res, next));
router.get("/featured", (req, res, next) => PropertyController_1.propertyController.getFeaturedProperties(req, res, next));
router.get("/search/location", (req, res, next) => PropertyController_1.propertyController.searchByLocation(req, res, next));
router.get("/:id", (req, res, next) => PropertyController_1.propertyController.getProperty(req, res, next));
// Protected routes - require authentication
router.use(authenticate_1.authenticate);
// Landlord routes (landlord and admin only)
router.post("/", (0, authenticate_2.authorize)(["landlord", "admin"]), (req, res, next) => PropertyController_1.propertyController.createProperty(req, res, next));
router.put("/:id", (0, authenticate_2.authorize)(["landlord", "admin"]), (req, res, next) => PropertyController_1.propertyController.updateProperty(req, res, next));
router.delete("/:id", (0, authenticate_2.authorize)(["landlord", "admin"]), (req, res, next) => PropertyController_1.propertyController.deleteProperty(req, res, next));
router.get("/landlord/my-properties", (0, authenticate_2.authorize)(["landlord", "admin"]), (req, res, next) => PropertyController_1.propertyController.getLandlordProperties(req, res, next));
router.patch("/:id/status", (0, authenticate_2.authorize)(["landlord", "admin"]), (req, res, next) => PropertyController_1.propertyController.togglePropertyStatus(req, res, next));
// Image management routes
router.patch("/:id/images", (0, authenticate_2.authorize)(["landlord", "admin"]), (req, res, next) => PropertyController_1.propertyController.updatePropertyImages(req, res, next));
exports.default = router;
//# sourceMappingURL=propertyRoutes.js.map