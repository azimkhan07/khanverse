import Hero from "../../components/Hero/Hero";
import TrustedCompanies from "../../components/TrustedCompanies/TrustedCompanies";
import Categories from "../Category/Categories";
import CTA from "../CTA/CTA";
import FeaturedGigs from "../FeaturedGigs/FeaturedGigs";
import Statistics from "../Statistics/Statistics";
import Testimonials from "../Testimonials/Testimonials";
import WhyKhanVerse from "../WhyKhanVerse/WhyKhanVerse";
import { SectionParallax } from "../../shared/components/SectionParallax";

function Home() {
    return (
        <>
            <Hero />

            <SectionParallax distance={40}>
                <TrustedCompanies />
            </SectionParallax>

            <SectionParallax distance={55}>
                <Categories />
            </SectionParallax>

            <SectionParallax distance={50}>
                <FeaturedGigs />
            </SectionParallax>

            <SectionParallax distance={60}>
                <WhyKhanVerse />
            </SectionParallax>

            <SectionParallax distance={45}>
                <Statistics />
            </SectionParallax>

            <SectionParallax distance={55}>
                <Testimonials />
            </SectionParallax>

            <SectionParallax distance={50}>
                <CTA />
            </SectionParallax>
        </>
    );
}

export default Home;
