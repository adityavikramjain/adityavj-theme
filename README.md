# Aditya V Jain - Personal Website

A fast, lightweight static site showcasing AI strategy consulting, academic sessions, and AI tools.

## ✨ Features

- **100% Static** - No backend, no database, just HTML/CSS/JavaScript
- **JSON-Powered** - All content managed through `data.json`
- **Advanced Filtering** - Filter sessions and resources by topic
- **Accordion Views** - Initially shows 2 rows (6 items), expandable
- **Workflow System** - Showcase multi-step AI prompt chains
- **Modal System** - View prompts and open Gemini Gems
- **Fully Responsive** - Mobile-optimized design
- **Electric Lab Design** - Custom design system with electric orange accents

## 📁 Project Structure

```
├── index.html          # Main page (converted from WordPress)
├── app.js              # All JavaScript functionality
├── style.css           # Complete styling (from WordPress theme)
├── data.json           # All content (sessions, resources, workflows)
└── README.md           # This file
```

## 🚀 Quick Start

### Local Development

1. **Clone or download** this repository

2. **Serve locally** with any static server:
   ```bash
   # Python
   python -m http.server 8000

   # Node.js (http-server)
   npx http-server

   # VS Code Live Server extension
   # Right-click index.html → "Open with Live Server"
   ```

3. **Open** `http://localhost:8000` in your browser

### Editing Content

All content is in `data.json`. Edit it directly:

#### Add a Session
```json
{
  "title": "Your Session Title",
  "institution": "Institution Name",
  "program": "Date",
  "url": "https://link-to-deck.com",
  "tag": "Session",
  "desc": null,
  "tags": ["Product Management", "AI"]
}
```

#### Add a Resource
```json
{
  "title": "Your Tool Name",
  "type": "Gemini Gem",
  "desc": "Tool description",
  "link": "https://gemini.google.com/gem/YOUR_GEM_ID",
  "tags": ["AI"],
  "prompt_text": ""
}
```

#### Add a Workflow
```json
{
  "type": "Workflow",
  "workflow_title": "Your Workflow Name",
  "workflow_desc": "Workflow description",
  "tags": ["AI", "Product Management"],
  "steps": [
    {
      "step_number": 1,
      "title": "Step 1 Name",
      "desc": "What this step does",
      "link": "https://gemini.google.com/gem/GEM_ID_1"
    },
    {
      "step_number": 2,
      "title": "Step 2 Name",
      "desc": "What this step does",
      "link": "https://gemini.google.com/gem/GEM_ID_2"
    }
  ]
}
```

## 🌐 Deployment

### Option 1: GitHub Pages (Recommended)

1. **Create a GitHub repository** (if you haven't):
   ```bash
   git init
   git add .
   git commit -m "Initial commit: Static site"
   git branch -M main
   git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO.git
   git push -u origin main
   ```

2. **Enable GitHub Pages**:
   - Go to repository Settings → Pages
   - Source: Deploy from branch `main`
   - Folder: `/` (root)
   - Save

3. **Access your site** at `https://YOUR_USERNAME.github.io/YOUR_REPO/`

### Option 2: Netlify

1. **Drag & Drop**:
   - Go to [netlify.com](https://netlify.com)
   - Drag your project folder to the deploy zone
   - Done! Your site is live

2. **Or use Git**:
   - Connect your GitHub repository
   - Build settings: Leave empty (static site)
   - Publish directory: `/`
   - Deploy!

### Option 3: Vercel

1. **Install Vercel CLI**:
   ```bash
   npm i -g vercel
   ```

2. **Deploy**:
   ```bash
   vercel
   ```

3. **Follow prompts** and your site is live!

### Option 4: Custom Domain

After deploying to GitHub Pages/Netlify/Vercel:
1. Buy a domain (Namecheap, GoDaddy, etc.)
2. Add custom domain in your host settings
3. Update DNS records as instructed

## 🎨 Customization

### Colors
Edit CSS variables in `style.css`:
```css
:root {
    --electric-orange: #FF4500;
    --charcoal: #1F2937;
    --mist-rose: #FFF5F2;
    --soft-grey: #6B7280;
    --white: #FFFFFF;
}
```

### Filtering Tags
Update filter buttons in `index.html` (lines 50-56 and 71-77):
```html
<button class="filter-button" data-filter="Your Topic">Your Topic</button>
```

### Profile Photo
Replace the blob image URL in `style.css` (line 42):
```css
.flux-blob {
    background-image: url('YOUR_IMAGE_URL');
}
```

## 🛠️ Tech Stack

- **HTML5** - Semantic markup
- **CSS3** - Custom properties, flexbox, grid
- **Vanilla JavaScript** - No frameworks, pure ES6+
- **JSON** - Data storage

## 📊 Features Detail

### Filtering System
- Click any tag to filter content
- Works independently for Sessions and Resources
- Live count display: "Showing X of Y"
- Maintains accordion state when filtering

### Accordion System
- Initially shows 6 cards (2 rows × 3 columns)
- "Show More" button expands to show all
- "Show Less" collapses and scrolls to section top
- Auto-hides button if ≤6 cards

### Workflow Cards
- Spans 2 columns for prominence
- Side-by-side sequential steps with arrow
- Each step clickable, opens Gemini Gem
- Stacks vertically on mobile

### Modal System
- Click prompt cards to view full text
- Copy button with clipboard API
- Click gem cards to view description + link
- ESC key to close, click overlay to close

## 🔧 Troubleshooting

**Filtering not working?**
- Hard refresh: `Ctrl+Shift+R` (Win) or `Cmd+Shift+R` (Mac)
- Check browser console for errors

**CORS errors locally?**
- Use a proper local server (see Quick Start)
- Can't open `index.html` directly due to `fetch()` restrictions

**Changes not reflecting?**
- Clear browser cache
- Hard refresh
- Check `data.json` syntax (use JSONLint.com)

## 📄 License

Personal project - All rights reserved.

## 📧 Contact

- **LinkedIn**: [aditya-jain-8895181](https://www.linkedin.com/in/aditya-jain-8895181/)
- **GitHub**: [adityavikramjain](https://github.com/adityavikramjain)
- **Email**: aditya1384@gmail.com

---

Built with ❤️ by Aditya V Jain
