# Button Testing Checklist - Kamar Show Page

## Location: `/kamar/{id}` (Room Detail Page)

### 1. Photo Navigation Buttons

- **Location:** Main photo carousel (top left)
- **Buttons:**
  - `<` (prevImage) - Left arrow to show previous photo
  - `>` (nextImage) - Right arrow to show next photo
- **Trigger:** `wire:click="prevImage"` / `wire:click="nextImage"`
- **Expected Behavior:**
  - [ ] Click left arrow → Previous photo displays
  - [ ] Click right arrow → Next photo displays
  - [ ] At first photo, left arrow wraps to last photo
  - [ ] At last photo, right arrow wraps to first photo
  - [ ] Only shows if room has multiple photos

### 2. Photo Thumbnail Selection

- **Location:** Below main photo carousel
- **Action:** Click on any thumbnail image
- **Trigger:** `wire:click="select({{ $i }})"`
- **Expected Behavior:**
  - [ ] Clicked thumbnail gets blue ring (ring-sky-400)
  - [ ] Main photo updates to show selected photo
  - [ ] Smooth transition between photos

### 3. Calendar Date Selection Button

- **Location:** Booking card on the right (blue dashed border box)
- **Button Text:** "Choose Date with Calendar" with calendar icon
- **Trigger:** `wire:click="openCalendar"`
- **Expected Behavior:**
  - [ ] Click button → Calendar section expands below
  - [ ] Shows current month/year with dates
  - [ ] Button should remain visible and clickable

### 4. Month Navigation Buttons (Inside Calendar)

- **Location:** Calendar header
- **Buttons:**
  - `‹` (previousMonth) - Left arrow for previous month
  - `›` (nextMonth) - Right arrow for next month
- **Trigger:** `wire:click="previousMonth"` / `wire:click="nextMonth"`
- **Expected Behavior:**
  - [ ] Click left arrow → Calendar shows previous month
  - [ ] Click right arrow → Calendar shows next month
  - [ ] Booked dates reload for new month
  - [ ] Past dates disable correctly for new month

### 5. Calendar Date Cells

- **Location:** Calendar grid (7 columns x 6 rows)
- **Types:**
  - Available dates (white background)
  - Past dates (gray, disabled)
  - Booked dates (gray, disabled)
  - Selected check-in (green)
  - Selected check-out (green)
  - Date range (light green)
- **Trigger:** `wire:click="selectDate({{ $day }})"`
- **Expected Behavior:**
  - [ ] Click available date → Shows as selected (green)
  - [ ] Shows price on available dates (e.g., "IDR 200.000")
  - [ ] Past dates show "NOT AVAILABLE" and are disabled
  - [ ] Booked dates show "NOT AVAILABLE" and are disabled
  - [ ] First click sets check-in date
  - [ ] Second click sets check-out date
  - [ ] Dates between check-in and check-out highlight light green
  - [ ] Date summary updates with nights count and total price

### 6. Book Now Button

- **Location:** Booking form at bottom of card
- **Trigger:** `<button class="ui-btn-primary">{{ __('rooms.book_now') }}</button>`
- **Expected Behavior:**
  - [ ] Click with dates selected → Submits form `wire:submit="pesan"`
  - [ ] Validates check-in and check-out dates
  - [ ] Shows error if dates are invalid
  - [ ] Redirects to booking confirmation page if successful
  - [ ] Shows warning if room is maintenance/unavailable
  - [ ] Disabled when room is not bookable (grayed out)

### 7. Back Button

- **Location:** Booking form at bottom of card
- **Action:** Link button
- **Expected Behavior:**
  - [ ] Click → Returns to home page
  - [ ] Link: `{{ route('home') }}`

### 8. WhatsApp Button

- **Location:** Booking form at bottom of card
- **Trigger:** Link to WhatsApp with pre-filled message
- **Expected Behavior:**
  - [ ] Click → Opens WhatsApp (if installed) with pre-filled message
  - [ ] Message includes room name and selected dates
  - [ ] Works when dates are not selected (shows "-" for dates)
  - [ ] Message language matches app locale (ID or EN)

### 9. Close Calendar Button

- **Location:** Bottom of calendar section (gray button)
- **Button Text:** "Close Calendar"
- **Trigger:** `wire:click="closeCalendar"`
- **Expected Behavior:**
  - [ ] Click → Calendar section collapses/hides
  - [ ] Selected dates persist in form
  - [ ] Date summary remains visible

---

## Test Scenarios

### Scenario 1: Normal Booking Flow

1. [ ] Load room page
2. [ ] Click "Choose Date with Calendar" button
3. [ ] Navigate months using ‹ › buttons
4. [ ] Select check-in date (should turn green)
5. [ ] Select check-out date (should turn green)
6. [ ] Verify date range highlights light green
7. [ ] Verify nights count and total price update
8. [ ] Click "Book Now" button
9. [ ] Should either submit or show validation error
10. [ ] Close calendar with "Close Calendar" button

### Scenario 2: Photo Navigation

1. [ ] Load room with multiple photos
2. [ ] Click left/right arrows to navigate
3. [ ] Click thumbnail images to select
4. [ ] Verify smooth transitions

### Scenario 3: Disabled Dates

1. [ ] Open calendar
2. [ ] Verify past dates are disabled (gray, "NOT AVAILABLE")
3. [ ] Verify booked dates are disabled (gray, "NOT AVAILABLE")
4. [ ] Try to click disabled dates (should not select)
5. [ ] Verify price only shows on available dates

### Scenario 4: WhatsApp Integration

1. [ ] Select check-in and check-out dates
2. [ ] Click "Ask via WhatsApp" button
3. [ ] Verify message contains room name and dates
4. [ ] Verify correct phone number is used

---

## Notes

- All buttons use Livewire wire:click for reactivity
- Calendar uses {!! $this->renderDayCell($day) !!} for rendering
- Date validation happens in selectDate() and recalc() methods
- Booked dates loaded from Pemesanan model with STATUS_PENDING/CONFIRMED
