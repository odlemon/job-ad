"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
// @ts-nocheck
const express_1 = __importDefault(require("express"));
const MediaController_1 = require("../controllers/MediaController");
const authenticate_1 = require("../middleware/authenticate");
const error_1 = require("../middleware/error");
const fileUpload_1 = require("../middleware/fileUpload");
const router = express_1.default.Router();
// All media routes require authentication
router.use(authenticate_1.authenticate);
// Profile image management
router.post("/profile-image", fileUpload_1.uploadProfileImage, (0, fileUpload_1.validateFileUpload)(['profileImage']), (0, error_1.asyncHandler)(MediaController_1.mediaController.uploadProfileImage.bind(MediaController_1.mediaController)));
router.delete("/profile-image", (0, error_1.asyncHandler)(MediaController_1.mediaController.deleteProfileImage.bind(MediaController_1.mediaController)));
// Document management
router.post("/documents", fileUpload_1.uploadDocuments, (0, fileUpload_1.validateFileUpload)(['documents']), (0, error_1.asyncHandler)(MediaController_1.mediaController.uploadDocuments.bind(MediaController_1.mediaController)));
router.delete("/documents/:documentIndex", (0, error_1.asyncHandler)(MediaController_1.mediaController.deleteDocument.bind(MediaController_1.mediaController)));
// Get user files
router.get("/files", (0, error_1.asyncHandler)(MediaController_1.mediaController.getUserFiles.bind(MediaController_1.mediaController)));
// File info
router.get("/info/:fileKey", (0, error_1.asyncHandler)(MediaController_1.mediaController.getFileInfo.bind(MediaController_1.mediaController)));
// Presigned URL for direct uploads
router.post("/presigned-url", (0, error_1.asyncHandler)(MediaController_1.mediaController.getPresignedUploadUrl.bind(MediaController_1.mediaController)));
exports.default = router;
//# sourceMappingURL=mediaRoutes.js.map