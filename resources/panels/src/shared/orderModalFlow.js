import { showDialog } from "./components/Dialog";

/**
 * Runs an order placement flow against the buyer orders endpoint.
 *
 * The `submit(force)` callback should build the payload and POST it to
 * `/buyer/orders`, adding `force: true` when the buyer explicitly chooses to
 * request a service from an unavailable seller.
 *
 * Handles the `seller_unavailable` 422 (offers "Request Anyway"), the
 * `profile_incomplete` 422, and generic failures — plus success navigation
 * to `/buyer/checkout/{order.id}`.
 */
export async function runOrderFlow(submit, { confirmText = "View Orders" } = {}) {
    try {
        const res = await submit(false);
        await showDialog({
            type: "success",
            title: "Order Placed",
            message: res.data?.message || "Your order has been placed and is awaiting seller approval.",
            confirmText,
        });
        window.location.href = `/buyer/checkout/${res.data?.order?.id ?? ""}`;
        return res;
    } catch (err) {
        const data = err.response?.data || {};

        if (data.error_code === "seller_unavailable") {
            const avail = data.availability;
            const when = avail
                ? `${avail.day_name}, ${avail.date} at ${avail.start_time} – ${avail.end_time}`
                : "soon";
            const ok = await showDialog({
                type: "info",
                title: "Seller Not Available Right Now",
                message:
                    `${data.message || "The seller is currently offline."} They will next be available on ${when}. ` +
                    "You can still send your request now — the seller will respond once back — or wait and order during their working hours.",
                confirmText: "Request Anyway",
                cancelText: "Cancel",
            });
            if (!ok) throw err;
            return runOrderFlow(() => submit(true), { confirmText });
        }

        if (data.error_code === "profile_incomplete") {
            await showDialog({
                type: "warning",
                title: "Complete Your Profile",
                message: data.message || "Please complete the profile section above to place your order.",
                confirmText: "OK",
            });
            throw err;
        }

        await showDialog({
            type: "danger",
            title: "Order Failed",
            message: data.message || "Could not place the order. Please try again.",
            confirmText: "OK",
        });
        throw err;
    }
}