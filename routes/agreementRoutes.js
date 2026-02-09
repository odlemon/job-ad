"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
// @ts-nocheck
const express_1 = __importDefault(require("express"));
const AgreementController_1 = require("../controllers/AgreementController");
const authenticate_1 = require("../middleware/authenticate");
const authenticate_2 = require("../middleware/authenticate");
const router = express_1.default.Router();
// All agreement routes require authentication
router.use(authenticate_1.authenticate);
// Get agreement templates (public for authenticated users)
router.get("/templates", (req, res, next) => AgreementController_1.agreementController.getAgreementTemplates(req, res, next));
router.get("/templates/:id", (req, res, next) => AgreementController_1.agreementController.getAgreementTemplate(req, res, next));
// Get user's agreements
router.get("/", (req, res, next) => AgreementController_1.agreementController.getUserAgreements(req, res, next));
// Get pending agreements for user
router.get("/pending", (req, res, next) => AgreementController_1.agreementController.getPendingAgreements(req, res, next));
// Get active agreements for user
router.get("/active", (req, res, next) => AgreementController_1.agreementController.getActiveAgreements(req, res, next));
// Get agreement statistics
router.get("/stats", (req, res, next) => AgreementController_1.agreementController.getAgreementStats(req, res, next));
// Get specific agreement by ID
router.get("/:id", (req, res, next) => AgreementController_1.agreementController.getAgreementById(req, res, next));
// Generate agreement PDF
router.get("/:id/pdf", (req, res, next) => AgreementController_1.agreementController.generateAgreementPDF(req, res, next));
// Get agreement signatures
router.get("/:id/signatures", (req, res, next) => AgreementController_1.agreementController.getAgreementSignatures(req, res, next));
// Get agreement audit trail
router.get("/:id/audit-trail", (req, res, next) => AgreementController_1.agreementController.getAgreementAuditTrail(req, res, next));
// Create new agreement (landlord only)
router.post("/", (0, authenticate_2.authorize)(["landlord"]), (req, res, next) => AgreementController_1.agreementController.createAgreement(req, res, next));
// Create agreement from template (landlord only)
router.post("/from-template", (0, authenticate_2.authorize)(["landlord"]), (req, res, next) => AgreementController_1.agreementController.createAgreementFromTemplate(req, res, next));
// Update agreement (landlord only)
router.put("/:id", (0, authenticate_2.authorize)(["landlord"]), (req, res, next) => AgreementController_1.agreementController.updateAgreement(req, res, next));
// Send agreement for review (landlord only)
router.post("/:id/review", (0, authenticate_2.authorize)(["landlord"]), (req, res, next) => AgreementController_1.agreementController.sendForReview(req, res, next));
// Activate agreement (landlord only)
router.post("/:id/activate", (0, authenticate_2.authorize)(["landlord"]), (req, res, next) => AgreementController_1.agreementController.activateAgreement(req, res, next));
// Sign agreement (both landlord and tenant)
router.post("/:id/sign", (req, res, next) => AgreementController_1.agreementController.signAgreement(req, res, next));
// Terminate agreement (both landlord and tenant)
router.post("/:id/terminate", (req, res, next) => AgreementController_1.agreementController.terminateAgreement(req, res, next));
// Upload attachment to agreement (both landlord and tenant)
router.post("/:id/attachments", (req, res, next) => AgreementController_1.agreementController.uploadAttachment(req, res, next));
// Verify signature (public for authenticated users)
router.get("/signatures/:signatureId/verify", (req, res, next) => AgreementController_1.agreementController.verifySignature(req, res, next));
exports.default = router;
//# sourceMappingURL=agreementRoutes.js.map