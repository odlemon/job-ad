"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
// @ts-nocheck
const express_1 = __importDefault(require("express"));
const ChatController_1 = require("../controllers/ChatController");
const authenticate_1 = require("../middleware/authenticate");
const router = express_1.default.Router();
// Apply authentication middleware to all routes
router.use(authenticate_1.authenticate);
// Chat management routes
router.post("/get-or-create", ChatController_1.chatController.getOrCreateChat.bind(ChatController_1.chatController));
router.get("/", ChatController_1.chatController.getUserChats.bind(ChatController_1.chatController));
router.get("/stats", ChatController_1.chatController.getChatStats.bind(ChatController_1.chatController));
router.get("/unread-count", ChatController_1.chatController.getUnreadCount.bind(ChatController_1.chatController));
// Chat-specific routes
router.get("/:chatId", ChatController_1.chatController.getChatById.bind(ChatController_1.chatController));
router.post("/:chatId/messages", ChatController_1.chatController.sendMessage.bind(ChatController_1.chatController));
router.put("/:chatId/read", ChatController_1.chatController.markMessagesAsRead.bind(ChatController_1.chatController));
router.delete("/:chatId", ChatController_1.chatController.archiveChat.bind(ChatController_1.chatController));
// Viewing request routes
router.post("/viewing-request", (0, authenticate_1.authorize)(["tenant"]), ChatController_1.chatController.sendViewingRequest.bind(ChatController_1.chatController));
router.put("/viewing-request/respond", (0, authenticate_1.authorize)(["landlord"]), ChatController_1.chatController.respondToViewingRequest.bind(ChatController_1.chatController));
router.get("/viewing-requests", (0, authenticate_1.authorize)(["landlord"]), ChatController_1.chatController.getLandlordViewingRequests.bind(ChatController_1.chatController));
// Move-in request routes
router.post("/move-in-request", (0, authenticate_1.authorize)(["tenant"]), ChatController_1.chatController.sendMoveInRequest.bind(ChatController_1.chatController));
router.put("/move-in-request/respond", (0, authenticate_1.authorize)(["landlord"]), ChatController_1.chatController.respondToMoveInRequest.bind(ChatController_1.chatController));
router.get("/move-in-requests", (0, authenticate_1.authorize)(["landlord"]), ChatController_1.chatController.getLandlordMoveInRequests.bind(ChatController_1.chatController));
// Tenant-specific routes
router.get("/pending-requests", (0, authenticate_1.authorize)(["tenant"]), ChatController_1.chatController.getTenantPendingRequests.bind(ChatController_1.chatController));
exports.default = router;
//# sourceMappingURL=chatRoutes.js.map