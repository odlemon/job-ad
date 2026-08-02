const express = require('express');
const multer = require('multer');
const path = require('path');
const fs = require('fs');
const cors = require('cors');

const app = express();
app.use(cors());

const PORT = Number(process.env.PORT || 3050);
const UPLOAD_DIR = process.env.UPLOAD_DIR || path.join(__dirname, 'uploads');
const PUBLIC_BASE = (process.env.PUBLIC_BASE_URL || 'http://127.0.0.1/uploads').replace(/\/$/, '');
const MAX_FILE_BYTES = Number(process.env.MAX_FILE_BYTES || 15 * 1024 * 1024);

const JOBHUB_DIRS = [
  'temp',
  'cv',
  'resumes',
  'documents',
  'profile-photos',
  'company-logos',
  'company-gallery',
  'company-covers',
  'certifications',
  'tender-documents',
  'application-documents',
  'job-documents',
  'cashbook-imports',
  'financial-reports',
  'term-sheets',
  'term-sheet-signatures',
];

fs.mkdirSync(UPLOAD_DIR, { recursive: true });
JOBHUB_DIRS.forEach((subdir) => {
  fs.mkdirSync(path.join(UPLOAD_DIR, subdir), { recursive: true });
});

app.use((req, res, next) => {
  console.log(`${new Date().toISOString()} - ${req.method} ${req.path}`);
  next();
});

const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    const uploadPath = path.join(UPLOAD_DIR, 'temp');
    fs.mkdirSync(uploadPath, { recursive: true });
    cb(null, uploadPath);
  },
  filename: (req, file, cb) => {
    const uniqueSuffix = `${Date.now()}-${Math.round(Math.random() * 1e9)}`;
    const ext = path.extname(file.originalname);
    const name = path.basename(file.originalname, ext).replace(/[^a-zA-Z0-9]/g, '_');
    cb(null, `${name}-${uniqueSuffix}${ext}`);
  },
});

const ALLOWED_MIME = new Set([
  'image/jpeg',
  'image/jpg',
  'image/png',
  'image/webp',
  'image/gif',
  'application/pdf',
  'application/msword',
  'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
  'application/vnd.ms-excel',
  'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
  'text/plain',
]);

const ALLOWED_EXT = new Set([
  '.jpg', '.jpeg', '.png', '.webp', '.gif',
  '.pdf', '.doc', '.docx', '.xls', '.xlsx', '.txt',
]);

function isAllowed(file) {
  const ext = path.extname(file.originalname || '').toLowerCase();
  if (ALLOWED_MIME.has(file.mimetype)) return true;
  // Some clients send octet-stream for office docs — allow by extension.
  if (file.mimetype === 'application/octet-stream' && ALLOWED_EXT.has(ext)) return true;
  return ALLOWED_EXT.has(ext);
}

const upload = multer({
  storage,
  limits: { fileSize: MAX_FILE_BYTES },
  fileFilter: (req, file, cb) => {
    if (isAllowed(file)) {
      cb(null, true);
    } else {
      cb(new Error(`Invalid file type (${file.mimetype}). Allowed: images, PDF, DOC/DOCX.`));
    }
  },
});

function sanitizeType(raw) {
  const normalized = String(raw || 'temp')
    .toLowerCase()
    .replace(/[^a-z0-9-]/g, '-');
  return JOBHUB_DIRS.includes(normalized) ? normalized : 'temp';
}

function publicUrl(type, filename) {
  return `${PUBLIC_BASE}/${type}/${filename}`;
}

function processUpload(req, res) {
  if (!req.files || req.files.length === 0) {
    return res.status(400).json({
      success: false,
      message: 'No files uploaded',
    });
  }

  const targetType = sanitizeType(req.body && req.body.type);
  const targetDir = path.join(UPLOAD_DIR, targetType);
  fs.mkdirSync(targetDir, { recursive: true });

  const files = req.files.map((file) => {
    const targetPath = path.join(targetDir, file.filename);
    try {
      fs.renameSync(file.path, targetPath);
    } catch (error) {
      return {
        url: publicUrl('temp', file.filename),
        filename: file.filename,
        originalname: file.originalname,
        size: file.size,
        mimetype: file.mimetype,
        error: `Failed to move to ${targetType} directory: ${error.message}`,
      };
    }

    return {
      url: publicUrl(targetType, file.filename),
      filename: file.filename,
      originalname: file.originalname,
      size: file.size,
      mimetype: file.mimetype,
    };
  });

  console.log(`Upload OK: ${files.length} file(s) -> ${targetDir}`);
  return res.json({ success: true, files });
}

app.post('/upload', upload.array('images', 10), processUpload);

app.post('/upload-documents', upload.array('documents', 15), (req, res) => {
  if (!req.body) req.body = {};
  if (!req.body.type) req.body.type = 'documents';
  return processUpload(req, res);
});

app.get('/health', (req, res) => {
  res.json({
    status: 'OK',
    uploadDir: UPLOAD_DIR,
    publicBase: PUBLIC_BASE,
  });
});

app.use((error, req, res, next) => {
  console.error('ERROR:', error.message);
  res.status(500).json({
    success: false,
    message: error.message || 'Internal server error',
  });
});

app.listen(PORT, '0.0.0.0', () => {
  console.log(`JobHub media server on :${PORT}`);
  console.log(`Upload dir: ${UPLOAD_DIR}`);
  console.log(`Public base: ${PUBLIC_BASE}`);
  console.log(`Health: http://127.0.0.1:${PORT}/health`);
});
