# Bayu CCTV UI Design Contract

## 1. Visual Direction

Industrial editorial: warm neutral surfaces, dark ink typography, rust accent, square controls, and high-contrast CTA areas. Light and dark modes keep the same hierarchy while swapping semantic surface and foreground tokens.

## 2. Color Tokens

- `--warm-white`: primary page/card surface.
- `--cream`: alternate section surface.
- `--ink`: primary text and inverse control fill. Never pair it with literal `white`; use `--warm-white` so contrast survives theme changes.
- `--text-muted`: secondary text.
- `--border`: neutral separators and control borders.
- `--rust`, `--rust-dark`: brand action and interactive emphasis.
- Literal white is reserved for intentionally permanent dark surfaces: hero overlays, CTA banner, and footer.

## 3. Typography

- Display: `--font-display` for headings and brand wordmarks.
- Body: `--font-body` for copy and controls.
- Controls use uppercase labels, 700 weight, and 1px tracking.

## 4. Spacing and Shape

- Base rhythm: 0.5rem increments.
- Primary controls: 0.85rem × 2rem.
- Compact controls: 0.5rem × 1.2rem.
- Editorial controls and cards remain square (`border-radius: 0`).

## 5. Interactive Primitives

- Hero ghost buttons are only for permanent dark/overlay surfaces.
- Buttons on theme-responsive surfaces use dedicated semantic classes with `--ink` foreground/fill and `--warm-white` inverse text.
- Hover/focus must preserve readable contrast in both themes.
- All icon-only or directional controls require accessible labels and visible `:focus-visible` outlines.

## 6. Theme Behavior

- Do not put theme-responsive `background`, `color`, or `border-color` declarations in inline styles.
- Component color pairs must use semantic tokens, not assumptions about whether `--ink` is dark.
- Test every changed interactive primitive in both `[data-bs-theme="light"]` and `[data-bs-theme="dark"]`.

## 7. Accessibility Constraints

- Normal text contrast target: WCAG AA (4.5:1).
- Focus indicator must remain visible against both neutral surfaces.
- Motion is limited to feedback for actual interactive state; honor reduced-motion rules already in `app.css`.

## 8. Accepted Debt

- Legacy layout-only inline styles remain in older views. Color-affecting inline declarations on theme-responsive surfaces are not accepted debt and should be migrated when touched.
