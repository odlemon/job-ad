"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
// @ts-nocheck
const express_1 = __importDefault(require("express"));
const FavoriteController_1 = require("../controllers/FavoriteController");
const authenticate_1 = require("../middleware/authenticate");
const authenticate_2 = require("../middleware/authenticate");
const router = express_1.default.Router();
// All favorite routes require authentication
router.use(authenticate_1.authenticate);
// Tenant-only routes
router.post("/", (0, authenticate_2.authorize)(["tenant"]), (req, res, next) => FavoriteController_1.favoriteController.addToFavorites(req, res, next));
router.delete("/:propertyId", (0, authenticate_2.authorize)(["tenant"]), (req, res, next) => FavoriteController_1.favoriteController.removeFromFavorites(req, res, next));
router.get("/", (0, authenticate_2.authorize)(["tenant"]), (req, res, next) => FavoriteController_1.favoriteController.getUserFavorites(req, res, next));
router.put("/:propertyId", (0, authenticate_2.authorize)(["tenant"]), (req, res, next) => FavoriteController_1.favoriteController.updateFavorite(req, res, next));
router.get("/check/:propertyId", (0, authenticate_2.authorize)(["tenant"]), (req, res, next) => FavoriteController_1.favoriteController.checkIfFavorited(req, res, next));
router.get("/reminders", (0, authenticate_2.authorize)(["tenant"]), (req, res, next) => FavoriteController_1.favoriteController.getUpcomingReminders(req, res, next));
router.post("/bulk", (0, authenticate_2.authorize)(["tenant"]), (req, res, next) => FavoriteController_1.favoriteController.bulkAddToFavorites(req, res, next));
router.get("/stats", (0, authenticate_2.authorize)(["tenant"]), (req, res, next) => FavoriteController_1.favoriteController.getFavoriteStats(req, res, next));
// Public route for getting favorite count (no auth required)
router.get("/count/:propertyId", (req, res, next) => FavoriteController_1.favoriteController.getPropertyFavoriteCount(req, res, next));
exports.default = router;
//# sourceMappingURL=favoriteRoutes.js.map