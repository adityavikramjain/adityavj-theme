# Data Migration Guide: JSON to WordPress

This guide explains how to migrate your existing data.json content into WordPress posts with ACF fields.

---

## Prerequisites

✅ **Before running the migration:**

1. **ACF field groups must be set up** (see ACF-FIELDS-SETUP.md)
2. **You must be logged in as WordPress admin**
3. **Backup your database** (optional but recommended)

---

## Migration Steps

### Step 1: Set Up ACF Fields

If you haven't already, create the ACF field groups as described in `ACF-FIELDS-SETUP.md`:
- Session Fields (for av_session)
- AI Resource Fields (for av_resource)

### Step 2: Run the Migration

1. **Log into WordPress admin**
2. **Visit this URL** (replace with your domain):
   ```
   https://yourdomain.com/?migrate_json_data=migrate123
   ```
3. **Wait for the migration to complete**
   - You'll see a success page showing all created posts
   - The page will list:
     - ✅ Sessions created
     - ✅ Resources created
     - ❌ Any errors (if they occurred)

### Step 3: Verify the Migration

1. Go to **WordPress Admin** → **Sessions**
   - You should see all 9 sessions from data.json
2. Go to **WordPress Admin** → **AI Resources**
   - You should see all 6 resources from data.json
3. Visit your **homepage**
   - All items should appear (WordPress posts + JSON entries)
   - WordPress posts will appear first, JSON entries after

### Step 4: Clean Up (Optional)

Once migration is verified:

**Option A: Keep Both** (Recommended for now)
- WordPress posts appear first
- JSON entries still work
- You can gradually remove JSON entries manually

**Option B: Remove JSON Entries**
- Edit `data.json`
- Remove migrated entries from `courses` and `resources` arrays
- Commit and push
- Only WordPress posts will appear

**Option C: Remove Migration Script**
- Edit `functions.php`
- Remove the entire "MIGRATION SCRIPT" section (lines 215-275)
- Commit and push
- Prevents accidental re-migration

---

## What Gets Migrated

### Sessions → av_session posts
From data.json `courses` array:
- ✅ Title → Post Title
- ✅ Institution → ACF field
- ✅ Program → ACF field
- ✅ URL → ACF field (session_url)
- ✅ Tag → ACF field
- ✅ Description → ACF field

### Resources → av_resource posts
From data.json `resources` array:
- ✅ Title → Post Title
- ✅ Type → ACF field (resource_type)
- ✅ Description → ACF field
- ✅ Link → ACF field (resource_link)
- ✅ Prompt Text → ACF field (prompt_text)

---

## Expected Results

After migration, you should have:

**9 Sessions:**
1. Customer Experience in the Age of GenAI
2. AI for Marketing Use-Cases
3. Agile methodology, Experiences and Perspectives
4. Product Management in Practice
5. Experimentation in Product Management
6. AI for Indian Sales Leaders: Workshop
7. AI 101 for Business Leaders
8. Understanding Customer Needs
9. Pricing in E-Commerce

**6 AI Resources:**
1. Ideation Engine (Prompt)
2. Context Extraction Expert For Building Prompts (Gemini Gem)
3. Warren Buffet Style Stock Analyzer Pt 2 (Gemini Gem)
4. Warren Buffet Style Stock Analyzer Gem Pt 1 (Gemini Gem)
5. Professional Report Formatter (Gemini Gem)
6. AI Consultant and Tutor for Indian Sales & Marketing Professionals (Gemini Gem)

---

## Troubleshooting

### "Nothing happened when I visited the URL"
- **Solution:** Make sure you're logged in as WordPress admin
- Check that you have "manage_options" capability

### "Blank page after migration"
- **Solution:** This is normal! Check WordPress admin to see if posts were created
- The script uses `wp_die()` which may not display on all servers

### "Some items missing"
- **Solution:** Check the ACF field groups are properly set up
- Field names must match exactly (case-sensitive):
  - `institution`, `program`, `session_url`, `tag`, `description`
  - `resource_type`, `resource_link`, `prompt_text`

### "Duplicate posts after running twice"
- **Solution:** The script doesn't check for duplicates
- Delete duplicate posts manually in WordPress admin
- Or restore from backup and run once

### "ACF fields are empty"
- **Solution:** ACF plugin might not be active
- Activate ACF plugin first
- Re-run migration after ACF is active

---

## Security Note

The migration URL includes a secret key (`migrate123`) and requires admin login for security.

**After migration:**
- The URL becomes inactive (you can run it again if needed)
- Consider removing the migration code from functions.php
- Change the secret key if you want to keep the function

---

## After Migration: Managing Content

Once migrated, you have two options for each new item:

### Option 1: Add via WordPress (Recommended)
1. Go to **Sessions** or **AI Resources** → **Add New**
2. Fill in title and ACF fields
3. Publish
4. ✅ Appears on site immediately

### Option 2: Add via data.json (Git-tracked)
1. Edit `data.json`
2. Add to `courses` or `resources` array
3. Commit and push
4. ✅ Appears on site

Both methods work together! WordPress posts appear first, JSON entries after.

---

## Next Steps

After successful migration:

1. ✅ Test creating a new Session via WordPress admin
2. ✅ Test creating a new Resource via WordPress admin
3. ✅ Verify all ACF fields are working
4. ✅ Check modal functionality for resources
5. ⚙️ Decide if you want to keep JSON entries or remove them
6. 🗑️ (Optional) Remove migration script from functions.php

---

Need help? Check ACF-FIELDS-SETUP.md for field configuration details.
