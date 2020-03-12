import GuestCartCapture from './GuestCartCapture';

// Capture abandoned carts from non-logged-in users.
const guestCartCapture = new GuestCartCapture();
guestCartCapture.init();