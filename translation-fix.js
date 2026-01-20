#!/usr/bin/env node

import fs from 'fs';
import path from 'path';

const DOMAIN = 'modular-ai-assistant';
const HANDLES = ['modular-ai-assistant-admin', 'modular-ai-assistant-frontend'];
const LANG_DIR = './resources/languages';

// Helper functions
const getLocale = (filename) => filename.match(/([a-z]{2}_[A-Z]{2})/)?.[1];
const isTargetFile = (filename) => {
    if (!filename.endsWith('.json')) return false;
    // Skip files that already have any of our handles in them
    return !HANDLES.some(handle => filename.includes(`-${handle}.json`));
};
const createFilename = (locale, handle) => `${DOMAIN}-${locale}-${handle}.json`;

// Get all translation files that need processing
const filesToProcess = fs.readdirSync(LANG_DIR).filter(isTargetFile);

if (filesToProcess.length === 0) {
    console.log('No translation files to process.');
    process.exit(0);
}

// Group and merge translations by locale
const locales = new Map();

filesToProcess.forEach(filename => {
    const locale = getLocale(filename);
    if (!locale) return;

    try {
        const data = JSON.parse(fs.readFileSync(path.join(LANG_DIR, filename), 'utf8'));
        const domainData = Object.values(data.locale_data || {})[0] || {};
        
        // Extract translations (excluding metadata)
        const translations = Object.fromEntries(
            Object.entries(domainData).filter(([key]) => key !== "")
        );

        if (!locales.has(locale)) {
            locales.set(locale, { files: [], translations: {} });
        }

        const localeData = locales.get(locale);
        localeData.files.push(filename);
        Object.assign(localeData.translations, translations);

    } catch (error) {
        console.warn(`Skipping ${filename}: ${error.message}`);
    }
});

// Create merged files for each handle
locales.forEach(({ files, translations }, locale) => {
    const mergedData = {
        "domain": DOMAIN,
        "locale_data": {
            [DOMAIN]: {
                "": { "domain": DOMAIN, "lang": locale, "plural-forms": "nplurals=2; plural=(n != 1);" },
                ...translations
            }
        }
    };

    // Create a file for each handle with the same translations
    HANDLES.forEach(handle => {
        const outputFile = createFilename(locale, handle);
        fs.writeFileSync(path.join(LANG_DIR, outputFile), JSON.stringify(mergedData, null, 2));
        console.log(`✓ ${outputFile} (${Object.keys(translations).length} strings from ${files.length} files)`);
    });

    // Clean up old files after creating all handle variants
    files.forEach(file => fs.unlinkSync(path.join(LANG_DIR, file)));
});
