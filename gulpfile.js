// require gulp
const { src, dest, series, parallel, task } = require('gulp');

// require gulp cleaner
const cleanc = require('gulp-clean');

// translations
const wpPot = require('gulp-wp-pot');

// fs
const fs = require('fs');

// read in our package json file
const pkg = JSON.parse(fs.readFileSync('./package.json'));

// directory structure
const dirs = {
  src: {
    root: pkg.source,
    assets: `${pkg.source}/assets`,
    css: `${pkg.source}/assets/css`,
    js: `${pkg.source}/assets/js`,
    vendor: `${pkg.source}/vendor/**/*`
  },
  dist: {
    root: pkg.distribution,
    assets: `${pkg.distribution}/assets`,
    css: `${pkg.distribution}/assets/css`,
    js: `${pkg.distribution}/assets/js`,
    vendor: `${pkg.distribution}/vendor/`
  }
};

// file patterns
const globs = {
  php: [
    `${dirs.src.root}/*.php`,
    `${dirs.src.root}/**/*.php`
  ],
  templates: [
    `${dirs.src.root}/*.php`,
    `${dirs.src.root}/**/*.php`,
    `${dirs.src.root}/screenshot.png`,
    `${dirs.src.root}/style.css`,
    `${dirs.src.root}/readme.txt`,
    `${dirs.src.root}/readme.md`,
    `${dirs.src.root}/composer.json`,
    `${dirs.src.root}/composer.lock`,
  ],
};

// Tasks
function cleanDist() {
  console.log('# Cleaning Up Distribution');
  return src([`${dirs.dist.root}/`], {
    read: false,
    allowEmpty: true,
    force: true
  }).pipe(cleanc());
}

function generateLanguages() {
  console.log('# Working on Languages');
  return src(globs.php)
    .pipe(wpPot({
      domain: pkg.name,
      package: pkg.package,
    }))
    .pipe(dest(`${dirs.dist.root}/languages/${pkg.name}.pot`));
}

function copyTemplates() {
  console.log('# Working on Templates');
  return src(globs.templates, { allowEmpty: true })
    .pipe(dest(`${dirs.dist.root}`));
}

function copyAssets() {
  console.log('# Working on Assets (non-minified)');
  return src([
    `${dirs.src.css}/style.css`,
    `${dirs.src.js}/script.js`,
  ], { allowEmpty: true })
    .pipe(dest(file => {
      return file.extname === '.css' ? dirs.dist.css : dirs.dist.js;
    }));
}

function copyVendor() {
  console.log('# Working on Vendor');
  return src(dirs.src.vendor, { allowEmpty: true })
    .pipe(dest(dirs.dist.vendor));
}

function copyToProduction(done) {
  if (pkg.production?.shouldcopy) {
    console.log('# Copying to Production');
    return src(`${dirs.dist.root}/**/*`, { allowEmpty: true })
      .pipe(dest(pkg.production.path));
  }
  return done();
}

// Ensure dist directories exist for esbuild
function createDistDirs(done) {
  const dirsToCreate = [
    dirs.dist.css,
    dirs.dist.js
  ];

  dirsToCreate.forEach(dir => {
    if (!fs.existsSync(dir)) {
      fs.mkdirSync(dir, { recursive: true });
    }
  });

  done();
}

// Define tasks
task('clean_dist', cleanDist);
task('languages', generateLanguages);
task('templates', copyTemplates);
task('assets', copyAssets);
task('vendor', copyVendor);
task('production_copy', copyToProduction);
task('create_dirs', createDistDirs);

// Default task - run with: npm run gulp
// Then run: npm run minify (or npm run build for both)
task('default', series(
  'clean_dist',
  'create_dirs',
  parallel('languages', 'templates', 'assets'),
  'vendor',
  'production_copy'
));