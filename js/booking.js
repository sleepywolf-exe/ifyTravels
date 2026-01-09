/**
 * Booking Page Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    const bookingForm = document.getElementById('booking-form');
    const errorMessage = document.getElementById('error-message');
    const submitBtn = bookingForm.querySelector('button[type="submit"]');

    if (bookingForm) {
        bookingForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Basic Frontend Validation
            const firstName = document.getElementById('first-name').value;
            const email = document.getElementById('email').value;

            if (!firstName || !email) {
                showError("Please fill in all required fields.");
                return;
            }

            // Disable button
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span class="animate-spin inline-block mr-2">&#9696;</span> Processing...`;
            errorMessage.classList.add('hidden');

            try {
                // Simulate Service Call / Payment
                // In reality, you'd create a booking record via API first, then get an Order ID for Razorpay

            }

            } catch (error) {
            console.error(error);
            showError(error.message);
            submitBtn.disabled = false;
            submitBtn.textContent = "Pay & Confirm Booking";
        }
    });
    }

function showError(msg) {
    errorMessage.textContent = msg;
    errorMessage.classList.remove('hidden');
}
});
