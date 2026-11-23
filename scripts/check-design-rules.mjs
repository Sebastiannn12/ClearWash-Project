import { readdir, readFile } from 'node:fs/promises';
import { extname, join, relative } from 'node:path';

const root = process.cwd();
const viewsRoot = join(root, 'resources', 'views');
const violations = [];

const walk = async (directory) => {
    const entries = await readdir(directory, { withFileTypes: true });
    const files = [];

    for (const entry of entries) {
        const path = join(directory, entry.name);
        files.push(...(entry.isDirectory() ? await walk(path) : [path]));
    }

    return files;
};

for (const path of await walk(viewsRoot)) {
    if (extname(path) !== '.php') {
        continue;
    }

    const source = await readFile(path, 'utf8');
    const file = relative(root, path);

    if (/text-red-\d+[^>]*>\s*\*\s*<\/span>/u.test(source)) {
        violations.push(`${file}: required fields must use .cw-required-dot, not a red asterisk`);
    }

    const controls = source.match(/<(?:input|select|textarea)\b[^>]*>/gsu) ?? [];
    for (const control of controls) {
        if (/\bdark:bg-(?!white\b)[^\s"']+/u.test(control)) {
            violations.push(`${file}: form controls must remain white in dark mode`);
            break;
        }
    }
}

const css = await readFile(join(root, 'resources', 'css', 'app.css'), 'utf8');
if (!/\.dark :is\(\.cw-workspace, \.cw-login\)[\s\S]*?background-color:\s*#fff;/u.test(css)) {
    violations.push('resources/css/app.css: missing the global white form-control rule for dark mode');
}

if (violations.length > 0) {
    console.error(violations.join('\n'));
    process.exitCode = 1;
} else {
    console.log('ClearWash design rules passed.');
}
