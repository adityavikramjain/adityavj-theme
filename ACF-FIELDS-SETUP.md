# ACF Fields Setup Guide

This document describes the ACF (Advanced Custom Fields) field groups you need to create in WordPress admin for the hybrid content management system.

## Prerequisites
- Install and activate **Advanced Custom Fields** (free or PRO version)
- The custom post types `av_session` and `av_resource` are automatically registered by the theme

---

## Field Group 1: Session Fields

**Location Rule:** Post Type is equal to `Sessions (av_session)`

### Fields:

| Field Label | Field Name | Field Type | Instructions | Required |
|------------|------------|------------|--------------|----------|
| Institution | `institution` | Text | e.g., "Guest Session", "IIM-K" | Yes |
| Program/Date | `program` | Text | e.g., "Nov 2025", "Fall 2024" | Yes |
| Session URL | `session_url` | URL | Link to presentation/deck | Yes |
| Tag | `tag` | Text | Default: "Session" | No |
| Description | `description` | Textarea | Optional description | No |

---

## Field Group 2: AI Resource Fields

**Location Rule:** Post Type is equal to `AI Resources (av_resource)`

### Fields:

| Field Label | Field Name | Field Type | Instructions | Required |
|------------|------------|------------|--------------|----------|
| Resource Type | `resource_type` | Select | Options: Gemini Gem, Custom GPT, Prompt | Yes |
| Resource Link | `resource_link` | URL | Link to gem/GPT | Conditional* |
| Prompt Text | `prompt_text` | Textarea | Full prompt text (for Prompt type only) | Conditional* |
| Description | `description` | Text | Optional description | No |

*Conditional Logic:
- Show `resource_link` when `resource_type` is "Gemini Gem" OR "Custom GPT"
- Show `prompt_text` when `resource_type` is "Prompt"

### Resource Type Select Options:
```
Gemini Gem : Gemini Gem
Custom GPT : Custom GPT
Prompt : Prompt
```

---

## Quick Setup Instructions

1. **Go to WordPress Admin** → Custom Fields → Field Groups
2. **Create Field Group: "Session Fields"**
   - Add all fields from Field Group 1 table
   - Set Location Rule: Post Type = Sessions
   - Save

3. **Create Field Group: "AI Resource Fields"**
   - Add all fields from Field Group 2 table
   - Set Location Rule: Post Type = AI Resources
   - Configure conditional logic for `resource_link` and `prompt_text`
   - Save

4. **Test the System**
   - Go to Sessions → Add New
   - Fill in fields and publish
   - Verify it appears on the front-page alongside JSON entries

---

## How the Hybrid System Works

```
WordPress Posts (via ACF) ──┐
                             ├──> Merged Array ──> Display on Site
data.json File             ──┘
```

- **WordPress posts are loaded FIRST** (appear at the top)
- **JSON entries are APPENDED** after WordPress entries
- Both sources appear together on the frontend
- You can add content via either method
- JSON entries are tracked in git for version control

---

## Usage Tips

### Adding via WordPress Admin (Recommended for Quick Edits):
1. Go to Sessions or AI Resources
2. Click "Add New"
3. Fill in the title and custom fields
4. Publish
5. Check front-page - new entry appears immediately

### Adding via data.json (Recommended for Bulk/Git Tracking):
1. Edit `data.json` in your code editor or via Claude Code
2. Add entries to `courses` or `resources` arrays
3. Commit and push to git
4. Entries appear on site (no WordPress admin needed)

### Reordering Items:
- WordPress items: Use "Order" field (post menu order) or drag-and-drop plugins
- JSON items: Reorder the array in data.json

---

## Example: Adding a New Resource via WordPress

1. **Navigate:** WordPress Admin → AI Resources → Add New
2. **Title:** "My New Gemini Gem"
3. **Resource Type:** Select "Gemini Gem"
4. **Resource Link:** https://gemini.google.com/gem/abc123
5. **Publish**

The gem will now appear in "The AI Lab" section on your homepage!

---

## Troubleshooting

**Q: My WordPress posts don't appear on the site**
- A: Make sure the posts are **Published** (not Draft)
- Check that ACF fields are filled correctly
- Clear any caching plugins

**Q: Do I need to fill all fields?**
- A: Only required fields (marked Yes) are mandatory
- The system uses sensible defaults for optional fields

**Q: Can I delete data.json entries?**
- A: Yes! Once you migrate content to WordPress, you can remove entries from data.json

**Q: What if ACF plugin is deactivated?**
- A: The system will fall back to data.json only (safe fallback)
