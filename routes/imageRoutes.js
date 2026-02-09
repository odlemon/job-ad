"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
// @ts-nocheck
const express_1 = __importDefault(require("express"));
const ImageController_1 = require("../controllers/ImageController");
const authenticate_1 = require("../middleware/authenticate");
const authenticate_2 = require("../middleware/authenticate");
const router = express_1.default.Router();
// All image routes require authentication
router.use(authenticate_1.authenticate);
// Upload single image
router.post("/upload", (0, authenticate_2.authorize)(["landlord", "admin"]), ImageController_1.ImageController.getUploadMiddleware(), (req, res, next) => ImageController_1.imageController.uploadImage(req, res, next));
// Upload multiple images
router.post("/upload-multiple", (0, authenticate_2.authorize)(["landlord", "admin"]), ImageController_1.ImageController.getMultipleUploadMiddleware(), (req, res, next) => ImageController_1.imageController.uploadMultipleImages(req, res, next));
// Delete image
router.delete("/:imageKey", (0, authenticate_2.authorize)(["landlord", "admin"]), (req, res, next) => ImageController_1.imageController.deleteImage(req, res, next));
exports.default = router;
//# sourceMappingURL=imageRoutes.js.map