console.log("Booking.js est bien chargé !");
document.addEventListener("DOMContentLoaded", function () {
    const summary = document.querySelector(".booking-summary");
    const countElement = document.getElementById("selected-count");
    const totalElement = document.getElementById("selected-total");

    if (!summary || !countElement || !totalElement) {
        return;
    }

    const price = Number(summary.dataset.price);

    function updateSummary() {
        const selectedSeats = document.querySelectorAll(
            ".seat.available input:checked",
        );

        const count = selectedSeats.length;
        const total = count * price;

        countElement.textContent = count;

        totalElement.textContent = total.toLocaleString("fr-FR", {
            style: "currency",
            currency: "EUR",
        });
    }

    document.querySelectorAll(".seat.available input").forEach((input) => {
        input.addEventListener("change", updateSummary);
    });

    updateSummary();
});
