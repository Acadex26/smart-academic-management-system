<?php
// frontend/admin/partials/styles.php
// Minimal custom CSS for patterns Tailwind can't cover:
// - CSS-only accordion (checkbox hack)
// - CSS custom properties used in JS-generated HTML
// - Pseudoelement ::before content tricks
// - Complex :checked combinators
// All other styles use Tailwind utility classes inline.
?>
<style>
  /* Font */
  * {
    font-family: 'Sora', sans-serif;
    box-sizing: border-box;
  }

  body {
    margin: 0;
    background: #f0f4f8;
  }

  /* Module panels — JS-toggled */
  .module-panel {
    display: none;
  }

  .module-panel.active {
    display: block;
  }

  /* CSS-only nav accordion */
  .nav-toggle {
    display: none;
  }

  .nav-toggle:checked~.nav-btn {
    color: #58a6ff;
    background: rgba(88, 166, 255, 0.15);
    border-left: 3px solid #58a6ff;
  }

  .nav-toggle:checked~.nav-btn .chevron {
    transform: rotate(90deg);
  }

  .sub-menu {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
  }

  .nav-toggle:checked~.sub-menu {
    max-height: 200px;
  }

  /* sub-item bullet via ::before */
  .sub-item::before {
    content: '·';
    margin-right: 8px;
    color: #58a6ff;
    font-size: 16px;
    line-height: 0;
    vertical-align: middle;
  }

  /* stat-card top accent bars */
  .stat-card {
    position: relative;
    overflow: hidden;
  }

  .stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
  }

  .stat-card.blue::before {
    background: linear-gradient(90deg, #58a6ff, #79c0ff);
  }

  .stat-card.green::before {
    background: linear-gradient(90deg, #3fb950, #56d364);
  }

  .stat-card.orange::before {
    background: linear-gradient(90deg, #d29922, #e3b341);
  }

  .stat-card.purple::before {
    background: linear-gradient(90deg, #bc8cff, #d2a8ff);
  }

  /* form-select arrow */
  .form-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%238b949e' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 32px;
    cursor: pointer;
  }

  /* Scrollbar */
  ::-webkit-scrollbar {
    width: 5px;
  }

  ::-webkit-scrollbar-track {
    background: transparent;
  }

  ::-webkit-scrollbar-thumb {
    background: #30363d;
    border-radius: 99px;
  }

  /* Table overflow */
  .table-scroll {
    overflow-x: auto;
  }

  /* Attendance radio accent */
  .att-radio-group input[type="radio"] {
    accent-color: #58a6ff;
  }

  /* Marks grade colors */
  .grade-a {
    color: #3fb950;
    font-weight: 700;
  }

  .grade-b {
    color: #58a6ff;
    font-weight: 700;
  }

  .grade-c {
    color: #d29922;
    font-weight: 700;
  }

  .grade-f {
    color: #f85149;
    font-weight: 700;
  }

  /* Section tabs */
  .section-tab.active {
    background: white;
    color: #1a202c;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
  }

  /* Chevron transition */
  .chevron {
    transition: transform 0.25s;
  }
</style>