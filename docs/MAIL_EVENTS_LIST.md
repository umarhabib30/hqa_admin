# List of All Possible Emails in HQA Admin

This document lists every email the application can send, grouped by area.  
**✅** = implemented and sent on event | **📋** = optional / admin-only / future

---

## 1. Authentication & Security

| # | Email | When sent | Recipient | Status |
|---|--------|-----------|-----------|--------|
| 1.1 | **Admin/Staff forgot password** | User requests password reset (users table) | Requester email | ✅ |
| 1.2 | **Alumni forgot password** | Alumni requests password reset | Requester email | ✅ |
| 1.3 | **Login OTP (admin)** | After admin submits valid email/password | Admin email | ✅ |

---

## 2. Sponsor Package

| # | Email | When sent | Recipient | Status |
|---|--------|-----------|-----------|--------|
| 2.1 | **New sponsor subscriber (admin)** | Someone subscribes to a sponsor package (Stripe paid) | Admin (configurable) | ✅ |
| 2.2 | **Sponsor subscription confirmation** | Same as above – thank you / receipt for the subscriber | Subscriber email | ✅ (added) |

---

## 3. Alumni Events

| # | Email | When sent | Recipient | Status |
|---|--------|-----------|-----------|--------|
| 3.1 | **New alumni event** | Admin creates a new alumni event | All alumni subscribers (`alumni_mails`) | ✅ (enabled) |
| 3.2 | **Alumni event registration confirmation** | Someone registers (and pays) for an alumni event | Attendee email | ✅ (added) |

---

## 4. PTO Events

| # | Email | When sent | Recipient | Status |
|---|--------|-----------|-----------|--------|
| 4.1 | **New PTO event** | Admin creates a new PTO event | All PTO subscribers (`pto_subscribe_mails`) | ✅ |
| 4.2 | **PTO event registration confirmation** | Someone registers (and pays) for a PTO event | Attendee email | ✅ (added) |

---

## 5. Donation & Booking

| # | Email | When sent | Recipient | Status |
|---|--------|-----------|-----------|--------|
| 5.1 | **Donation booking ticket** | Someone completes a donation event booking (with payment) | Booker email (with QR/ticket) | ✅ |

---

## 6. Contact & Inquiries

| # | Email | When sent | Recipient | Status |
|---|--------|-----------|-----------|--------|
| 6.1 | **Contact sponsor form** | Someone submits the contact sponsor form | Admin (configurable) | ✅ |

---

## Summary

- **Implemented and sent on event:** 2.1, 2.2, 3.1, 3.2, 4.1, 4.2, 5.1, 6.1, plus all auth emails (1.1–1.3).
- **Subscriber lists:** Alumni events → `alumni_mails.email`; PTO events → `pto_subscribe_mails.email`.
- **Admin/contact recipient:** Set `MAIL_ADMIN_EMAIL` in `.env` to receive new sponsor subscriber (admin copy) and contact sponsor form emails. If not set, contact sponsor form still sends to a fallback address; sponsor admin copy is only sent when `MAIL_ADMIN_EMAIL` is set.
- **Queue:** Event and notification emails are queued (`Mail::queue()` / `->queue()`). Run `php artisan queue:work` so emails are actually sent.
