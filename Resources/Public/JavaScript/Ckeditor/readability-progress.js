import {html, LitElement} from 'lit';
import {styleTag} from '@typo3/core/lit-helper.js';

/**
 * Readability progress indicator used in the DeepL Write CKEditor overlay.
 *
 * Renders a full-width rainbow scale with a slim marker and a percentage label
 * positioned at the current Flesch reading-ease score (0-100).
 *
 * Styling lives in the shadow DOM, so it cannot leak into or be influenced by
 * other components. Instead of `static styles` (which relies on a constructable
 * stylesheet bound to the realm where the module was evaluated), the CSS is
 * emitted as a nonce-carrying `<style>` element via TYPO3's `styleTag` helper.
 * This is required because `@typo3/backend/modal.js` relocates the overlay
 * content into the top-level backend document: a stylesheet constructed in the
 * FormEngine iframe realm cannot be adopted into a shadow root owned by the top
 * document ("Adopted style sheet's constructor document must match ..."). A
 * `<style>` rendered into the template is created in the shadow root's own
 * document and carries the `litNonce` of that document, so it is both
 * document-safe and Content-Security-Policy compliant (`style-src 'nonce-...'`).
 *
 * The dynamic position is passed through the `--value` custom property via
 * CSSOM (`style.setProperty`), which is not governed by CSP.
 *
 * Usage:
 *   <deepl-write-readability value="0"></deepl-write-readability>
 *   element.value = 42.35; // reactive, re-renders
 */
const componentCss = `
  :host {
    --value: 0; /* 0-100, overridden per instance via CSSOM */

    position: relative;
    display: block;
    width: 100%;
    height: 20px;
    margin-top: 20px;
    margin-bottom: 10px;
    border-radius: var(--typo3-input-border-radius);

    /* Full-width rainbow background (not clipped) */
    background: linear-gradient(
      to right,
      #ff0000,
      #ff7a00,
      #ffd400,
      #a8ff00,
      #00a400
    );
    box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.08);
  }

  /* Slim vertical marker showing the current percentage */
  .marker {
    position: absolute;
    top: -4px;
    bottom: -4px;
    left: calc(var(--value) * 1%);
    width: 2px;
    transform: translateX(-1px);
    border-radius: 2px;
    background: #111;
    box-shadow:
      0 0 0 2px #fff,
      0 0 0 3px rgba(0, 0, 0, 0.06);
    pointer-events: none;
  }

  /* Percentage label that follows the marker */
  .label {
    position: absolute;
    top: -22px;
    left: calc(var(--value) * 1%);
    height: 18px;
    padding: 0 6px;
    transform: translateX(-50%);
    display: inline-flex;
    align-items: center;
    border-radius: 3px;
    background: #fff;
    border: 1px solid rgba(0, 0, 0, 0.12);
    font: 600 12px/1 system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    color: #111;
    white-space: nowrap;
    user-select: none;
    pointer-events: none;
  }
`;

export class ReadabilityProgress extends LitElement {
  static properties = {
    value: {type: Number},
  };

  constructor() {
    super();
    this.value = 0;
  }

  render() {
    const value = this.clampedValue;
    // Use the element's own document window so the `<style>` carries the nonce of
    // the document it actually lives in (top document when shown in a modal).
    return html`
      ${styleTag(componentCss, this.ownerDocument.defaultView)}
      <span class="marker" aria-hidden="true"></span>
      <span class="label">${value.toFixed(2)}%</span>
    `;
  }

  updated() {
    const value = this.clampedValue;
    // CSSOM write (not a CSP style-src-attr concern) drives marker/label position.
    this.style.setProperty('--value', String(value));
    // Expose progress semantics on the host element for assistive technology.
    this.setAttribute('role', 'progressbar');
    this.setAttribute('aria-valuemin', '0');
    this.setAttribute('aria-valuemax', '100');
    this.setAttribute('aria-valuenow', value.toFixed(2));
  }

  get clampedValue() {
    return Math.max(0, Math.min(100, Number(this.value) || 0));
  }
}

customElements.define('deepl-write-readability', ReadabilityProgress);
