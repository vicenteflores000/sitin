import { mkdir, copyFile } from 'fs/promises';
import path from 'path';

const root = process.cwd();
const targetDir = path.join(root, 'public', 'vendor', 'fullcalendar');

const sources = [
  {
    from: path.join(root, 'node_modules', '@fullcalendar', 'core', 'index.global.min.js'),
    to: path.join(targetDir, 'core.min.js'),
  },
  {
    from: path.join(root, 'node_modules', '@fullcalendar', 'daygrid', 'index.global.min.js'),
    to: path.join(targetDir, 'daygrid.min.js'),
  },
  {
    from: path.join(root, 'node_modules', '@fullcalendar', 'timegrid', 'index.global.min.js'),
    to: path.join(targetDir, 'timegrid.min.js'),
  },
  {
    from: path.join(root, 'node_modules', '@fullcalendar', 'interaction', 'index.global.min.js'),
    to: path.join(targetDir, 'interaction.min.js'),
  },
];

await mkdir(targetDir, { recursive: true });

for (const file of sources) {
  await copyFile(file.from, file.to);
}

console.log('FullCalendar assets copied to public/vendor/fullcalendar');
