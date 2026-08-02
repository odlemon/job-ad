module.exports = {
  apps: [
    {
      name: 'job-ad-media',
      cwd: __dirname,
      script: 'server.js',
      instances: 1,
      autorestart: true,
      watch: false,
      max_memory_restart: '256M',
      env: {
        PORT: 3050,
        UPLOAD_DIR: '/var/www/job-ad/uploads',
        PUBLIC_BASE_URL: 'http://207.180.234.151/uploads',
        MAX_FILE_BYTES: String(15 * 1024 * 1024),
      },
    },
  ],
};
