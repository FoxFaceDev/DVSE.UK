---
name: DVSE.UK
colors:
  surface: '#f9f9ff'
  surface-dim: '#cfdaf2'
  surface-bright: '#f9f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f0f3ff'
  surface-container: '#e7eeff'
  surface-container-high: '#dee8ff'
  surface-container-highest: '#d8e3fb'
  on-surface: '#111c2d'
  on-surface-variant: '#424751'
  inverse-surface: '#263143'
  inverse-on-surface: '#ecf1ff'
  outline: '#737783'
  outline-variant: '#c2c6d3'
  surface-tint: '#255dad'
  primary: '#00346f'
  on-primary: '#ffffff'
  primary-container: '#004a99'
  on-primary-container: '#9bbdff'
  inverse-primary: '#abc7ff'
  secondary: '#505f76'
  on-secondary: '#ffffff'
  secondary-container: '#d0e1fb'
  on-secondary-container: '#54647a'
  tertiary: '#323537'
  on-tertiary: '#ffffff'
  tertiary-container: '#494c4e'
  on-tertiary-container: '#babcbe'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d7e2ff'
  primary-fixed-dim: '#abc7ff'
  on-primary-fixed: '#001b3f'
  on-primary-fixed-variant: '#00458f'
  secondary-fixed: '#d3e4fe'
  secondary-fixed-dim: '#b7c8e1'
  on-secondary-fixed: '#0b1c30'
  on-secondary-fixed-variant: '#38485d'
  tertiary-fixed: '#e0e3e5'
  tertiary-fixed-dim: '#c4c7c9'
  on-tertiary-fixed: '#191c1e'
  on-tertiary-fixed-variant: '#444749'
  background: '#f9f9ff'
  on-background: '#111c2d'
  surface-variant: '#d8e3fb'
typography:
  headline-lg:
    fontFamily: Lexend
    fontSize: 32px
    fontWeight: '700'
    lineHeight: '1.2'
  headline-md:
    fontFamily: Lexend
    fontSize: 24px
    fontWeight: '600'
    lineHeight: '1.3'
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: '1.6'
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.5'
  label-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: '1.4'
    letterSpacing: 0.02em
  admin-data:
    fontFamily: Inter
    fontSize: 13px
    fontWeight: '500'
    lineHeight: '1.2'
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  base: 4px
  xs: 8px
  sm: 16px
  md: 24px
  lg: 40px
  xl: 64px
  gutter: 20px
---

## Brand & Style

The design system is anchored in the principles of **Academic Integrity, Clarity, and Accessibility**. As an educational testing platform, the UI must fade into the background to allow the content—the questions and data—to take center stage. The brand personality is "The Silent Proctor": authoritative yet supportive, professional, and unbiased.

The visual style is **Corporate Modern**. It utilizes a flat UI architecture to minimize cognitive load during high-stakes testing, while employing subtle shadows to provide tactile affordances for interactive elements. This approach ensures that the platform feels like a reliable tool rather than a distracting "app." The dual-language requirement (English/Kurdish) necessitates a layout that respects both Left-to-Right (LTR) and Right-to-Left (RTL) reading patterns without losing structural integrity.

## Colors

The palette is led by **Professional Blue (#004a99)**, a color that evokes trust and institutional stability. 

- **Primary**: Used for core branding, primary actions, and active states.
- **Secondary**: A muted slate used for sub-navigation and secondary interface elements to prevent visual fatigue.
- **Functional Colors**: Success Green (#16a34a) and Error Red (#dc2626) are reserved strictly for feedback. In the context of testing, these must be used with high-contrast text labels to ensure accessibility for color-blind users.
- **Surface & Background**: A very light grey/blue tertiary color is used for the admin panel's data-heavy backgrounds to separate content modules without the harshness of pure white.

## Typography

This design system uses a pairing of **Lexend** and **Inter**. 

**Lexend** was specifically designed to reduce visual stress and improve reading proficiency, making it the ideal choice for headings and test questions. 

**Inter** is used for the UI framework, buttons, and data-heavy admin tables due to its exceptional legibility at small sizes and its robust support for both Latin and Kurdish (Arabic script) characters. The system prioritizes generous line heights (1.5 - 1.6) for body text to ensure that long-form educational content remains readable during extended testing sessions.

## Layout & Spacing

The system employs a **hybrid grid approach**:

1.  **Mobile-First User View**: A 4-column fluid grid for test-takers. Content is stacked vertically to maximize focus. Margins are set at 16px to ensure content doesn't hit the screen edges on narrow devices.
2.  **Admin Panel**: A 12-column fixed-width grid for large screens. This allows for a "Sidebar + Dashboard" configuration where data density is high. 

A strict 4px/8px baseline shift is used to maintain vertical rhythm. Large "Safe Areas" (40px+) are used between major sections of a test to prevent accidental clicks on "Next" or "Submit" buttons.

## Elevation & Depth

This design system uses **Low-Contrast Outlines** supplemented by **Ambient Shadows** for interactive elements only.

- **Level 0 (Flat)**: Backgrounds and non-interactive containers.
- **Level 1 (Subtle Shadow)**: Buttons and Cards. The shadow is highly diffused (12px blur, 5% opacity, primary blue tint) to give a "lifted" feel without looking dated.
- **Level 2 (Active)**: Used for hovered states or focused input fields, where the shadow slightly increases in intensity and the border color shifts to the Primary Blue.

Depth is used sparingly to signify "clickability," while the rest of the interface remains flat to emphasize data and text.

## Shapes

The design system uses a **Soft (0.25rem)** roundedness level. This choice strikes a balance between the "sharpness" of traditional academic software and the "friendliness" of modern web apps. 

- **Standard Elements**: Buttons and inputs use the base 4px radius.
- **Containers**: Large cards and admin panels use `rounded-lg` (8px) to soften the layout.
- **Progress Indicators**: Success/Error chips use a fully rounded "pill" shape to distinguish them from actionable buttons.

## Components

### Buttons
Primary buttons are solid #004a99 with white text and a subtle shadow. Secondary buttons use a transparent background with a 1px border in the Primary Blue. For test navigation (Next/Back), buttons must be at least 48px tall for mobile tap targets.

### Input Fields
Inputs use a light grey background (#f1f5f9) with a bottom-border that transforms into a 2px Primary Blue border on focus. This provides a clear visual cue for the active question.

### Test Cards
Questions are housed in white cards with a 1px #e2e8f0 border. On mobile, these cards should span the full width to maximize horizontal space for text.

### Admin Data Tables
Admin tables use a "Zebra-stripe" pattern with the Tertiary color (#f8fafc) and minimal cell padding to allow for high data density. Headers are sticky and use the Label-MD typography style for clarity.

### Feedback Chips
For test results, use "Success" and "Error" chips. These are semi-transparent versions of the state colors with bold, high-contrast text and a matching icon (Checkmark/Cross) to ensure meaning is conveyed beyond color alone.