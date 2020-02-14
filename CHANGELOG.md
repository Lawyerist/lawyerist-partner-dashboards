# Changelog for Lawyerist Partner Dashboards

All notable changes to this project will be documented in this file. The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

## [0.3.2] - 2020-02-14

### [Fixed]
- Fix product page report when the page path returns no results.

## [0.3.1] - 2020-02-14

### [Fixed]
- Fix undefined index errors.


## [0.3.0] - 2020-02-14

### [Added]
- Add button to Partner to go straight to the dashboard for that partner.
- Allow a Partner to be associated with multiple product pages.

### [Changed]
- Don't show Affinity Claims Report tab if there aren't any affinity claims.


## [0.2.0] - 2020-02-05

### [Added]
- Options page for Google Analytics view ID.

### [Changed]
- Remove unused file lpd-frontend.js.

### [Fixed]
- Fix some layout issues.


## [0.1.0] - 2020-02-05

### [Added]
- Partner custom post type.
- Create a Partner Dashboards page if it doesn't already exist.
- Navigation for users with access to multiple dashboards.
- Partner dashboard template.
  - Logo and title.
  - Product page card.
    - Date filters (this month, last month, this year, and last year).
    - Portal and product page views from Google Analytics.
    - Product rating.
    - Trial button clicks.
  - Affinity benefit claims.
    - Status with updates.
  - Authorized user card.
