# UI Design Guide: Soft UI & Card-Based Layout
**This document serves as a system prompt / style guide for AI Agents generating user interface (UI) designs.**

## 1. Main Visual Concept (Vibe & Aesthetic)
- **Style Name:** Soft UI / Card-Based Layout / Modern Friendly.
- **Impression:** Soft, friendly, clean, and modern.
- **Key Elements:** Soft drop shadows, high border-radius (very rounded corners), and 2D Corporate Memphis style illustrations.

## 2. Color Palette (Flexible)
The specific color scheme is flexible and can be adapted to any brand or theme. However, the application of colors must follow these soft-UI rules:

- **Global App Background:** Use a very light, soft background color (e.g., off-white, light gray, or a very pale, low-opacity tint of the primary color). Avoid harsh or highly saturated backgrounds.
- **Card/Container Background:** Pure White (`#FFFFFF`) or a subtle color that slightly contrasts with the global background, allowing the cards to float visually.
- **Primary Accent Colors:** Apply the main brand color(s) to key interactive elements like primary CTAs, active states, and main highlights.
- **Secondary Accent Colors:** Use complementary or softer tones for secondary buttons, tags, or decorative elements.
- **Typography Colors:**
  - **Heading/Main Text:** Use a very dark, high-contrast color. *Avoid pure black (#000000)* to maintain the soft aesthetic; instead, use deep grays or dark shades of the primary brand color.
  - **Body/Supporting Text:** Use a muted, medium-contrast color (e.g., medium gray) for readability.

## 3. Typography
- **Font Family:** Use clean, geometric, and rounded Sans-Serif fonts. (Recommended: *Poppins, Nunito, Quicksand, or Gilroy*).
- **Hierarchy:**
  - **Heading (H1, H2):** Bold (700), proportionally larger size, dark color.
  - **Body:** Regular (400) or Medium (500), highly legible.
  - **Caption/Tags:** Semi-Bold (600), small size (10-12px).

## 4. Shapes & UI Elements
Every element should feel "soft" and not rigid. **FORBIDDEN: Using sharp corners (0px border-radius).**

- **Cards:**
  - **Border-radius:** Large, around `20px` to `30px`.
  - **Shadows:** Shadows must be very soft, widely diffused, and have low opacity. CSS Example: `box-shadow: 0px 15px 35px rgba(0, 0, 0, 0.05);`.
  - **Padding:** Spacious and airy, at least `16px` to `24px` inside the cards (Whitespace is key).
- **Buttons:**
  - **Shape:** Pill-shaped (fully rounded at the ends, `border-radius: 50px`) or heavily rounded rectangles (`border-radius: 12px - 16px`).
  - **Type:** Solid color for primary CTAs, borderless.
- **Input Fields & Search Bars:**
  - Very light background to differentiate from the card background.
  - No outer border, or a very thin/transparent border.
  - Pill-shaped or heavily rounded border-radius.

## 5. Illustrations & Iconography
- **Illustrations:** Use **Flat Vector / Corporate Memphis** style.
  - Outline-less human characters.
  - Casual or abstract body proportions (e.g., slightly larger or longer limbs).
  - Flat block colors that coordinate harmoniously with the chosen color scheme.
- **Icons:** 
  - Use icons with medium stroke weight (1.5px - 2px).
  - Rounded caps and joins.
  - Can be combined with small circular backgrounds in soft tint colors.

## 6. Layout & Spacing
- **Content Separation:** Use a *Card-based layout*. Do not separate content using hard divider lines; instead, use floating cards over the background.
- **Margin & Padding:** Provide consistent gaps between cards (e.g., 16px or 24px). Do not cramp elements closely together.

---
**AI Execution Instructions (Prompting Rule):**
"When generating the UI, apply a color palette of your choice, ensuring the overall aesthetic remains soft and modern. Use a light background, organize all content into clean cards with highly rounded corners (min 20px) and very soft drop shadows. Add cheerful 2D flat vector character illustrations, and use pill-shaped buttons. Do not use sharp corners, harsh dark shadows, or pure black text."