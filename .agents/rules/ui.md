# 🎨 UI & Design System Rules

## 🟠 Core Colors
- **Primary**: `#E87722` (Orange)
- **Secondary**: `White` or `Gray-50`
- **Text**: `Gray-900` for headings, `Gray-600` for body.

## 🚫 Negative Constraints (IMPORTANT)
- **NO GRADIENTS**: Do NOT use `bg-gradient-to-...`. Icons, backgrounds, and buttons must use **Solid Colors**.
- **No Inline Styles**: Use Tailwind classes or `assets/css/custom.css`.
- **No Browser Defaults**: Always style buttons and inputs using the premium theme.

## ✨ Aesthetics
- **Shadows**: `shadow-card` for normal cards, `shadow-card-lg` for popups.
- **Radius**: `rounded-xl` for containers, `rounded-lg` for interactive elements.
- **Feedback**: Use **SweetAlert2** for all alerts.
    - Success: Toast notification.
    - Error/Warning: Modal dialog.
