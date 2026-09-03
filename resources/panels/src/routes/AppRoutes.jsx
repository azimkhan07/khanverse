import { BrowserRouter, Routes, Route } from "react-router-dom";

import ScrollToTop from "../shared/components/ScrollToTop";
import PublicLayout from "../layouts/PublicLayout";
import GuestLayout from "../layouts/GuestLayout";

import Home from "../pages/Home/Home";
import Login from "../pages/Auth/Login";
import Register from "../pages/Auth/Register";
import ForgotPassword from "../pages/Auth/ForgotPassword";
import ResetPassword from "../pages/Auth/ResetPassword";
import VerifyEmail from "../pages/Auth/VerifyEmail";
import About from "../pages/About/About";
import FAQ from "../pages/FAQ/FAQ";
import NotFound from "../pages/NotFound/NotFound";
import CookiePolicy from "../pages/CookiePolicy/CookiePolicy";
import TermsConditions from "../pages/TermsConditions/TermsConditions";
import PrivacyPolicy from "../pages/PrivacyPolicy/PrivacyPolicy";
import Careers from "../pages/Careers/Careers";
import Blog from "../pages/Blog/Blog";
import BlogDetail from "../pages/Blog/BlogDetail";
import Contact from "../pages/Contact/Contact";
import Pricing from "../pages/Pricing/Pricing";
import ServiceListing from "../pages/Category/ServiceListing";
import ServiceDetail from "../pages/Category/ServiceDetail";

function AppRoutes() {
    return (
        <BrowserRouter>
            <ScrollToTop />
            <Routes>
                <Route element={<PublicLayout />}>
                    <Route index element={<Home />} />
                    <Route path="/about" element={<About />} />
                    <Route path="/pricing" element={<Pricing />} />
                    <Route path="/faq" element={<FAQ />} />
                    <Route path="/contact" element={<Contact />} />
                    <Route path="/blog" element={<Blog />} />
                    <Route path="/blog/:slug" element={<BlogDetail />} />
                    <Route path="/careers" element={<Careers />} />
                    <Route path="/privacy-policy" element={<PrivacyPolicy />} />
                    <Route path="/terms-conditions" element={<TermsConditions />} />
                    <Route path="/cookie-policy" element={<CookiePolicy />} />
                    <Route path="/category/:slug" element={<ServiceListing />} />
                    <Route path="/service/:id" element={<ServiceDetail />} />
                </Route>
                <Route element={<GuestLayout />}>
                    <Route path="/login" element={<Login />} />
                    <Route path="/register" element={<Register />} />
                    <Route path="/forgot-password" element={<ForgotPassword />} />
                    <Route path="/reset-password/:token" element={<ResetPassword />} />
                    <Route path="/verify-email" element={<VerifyEmail />} />
                </Route>
                <Route path="*" element={<NotFound />} />
            </Routes>
        </BrowserRouter>
    );
}

export default AppRoutes;
