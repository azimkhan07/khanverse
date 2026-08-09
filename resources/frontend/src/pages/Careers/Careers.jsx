import PageHero from "../../components/common/PageHero";
import PageContainer from "../../components/common/PageContainer";
import SectionHeading from "../../components/common/SectionHeading";

import careers from "../../data/careers";

import "../../theme/css/careers.css";

function Careers() {

    return (

        <>

            <PageHero
                title="Careers"
                subtitle="Join our growing team and build the future with us."
            />

            <PageContainer>

                <SectionHeading
                    tag="CAREERS"
                    title="Current Openings"
                    subtitle="Explore exciting career opportunities at KhanVerse."
                />

                <div className="career-grid">

                    {

                        careers.map((job)=>(

                            <div
                                className="career-card"
                                key={job.id}
                            >

                                <h3>

                                    {job.title}

                                </h3>

                                <div className="career-meta">

                                    <span>{job.department}</span>

                                    <span>{job.location}</span>

                                    <span>{job.type}</span>

                                </div>

                                <p>

                                    {job.description}

                                </p>

                                <button>

                                    Apply Now

                                </button>

                            </div>

                        ))

                    }

                </div>

            </PageContainer>

        </>

    );

}

export default Careers;
