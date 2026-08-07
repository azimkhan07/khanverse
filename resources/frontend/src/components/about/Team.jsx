import team1 from "../../theme/images/2.jpg";
import team2 from "../../theme/images/4.jpg";
import team3 from "../../theme/images/2.jpg";
import team4 from "../../theme/images/4.jpg";

import { FaGithub, FaLinkedin, FaTwitter } from "react-icons/fa";

function Team() {

    const members = [

        {
            id:1,
            image:team1,
            name:"Azim Khan",
            role:"Founder & CEO"
        },

        {
            id:2,
            image:team2,
            name:"Sarah Wilson",
            role:"UI / UX Designer"
        },

        {
            id:3,
            image:team3,
            name:"Michael James",
            role:"Backend Developer"
        },

        {
            id:4,
            image:team4,
            name:"Emily Brown",
            role:"Support Manager"
        }

    ];

    return (

        <section className="about-team">

            <div className="container">

                <div className="section-heading">

                    <span className="section-tag">

                        OUR TEAM

                    </span>

                    <h2>

                        Meet Our Amazing Team

                    </h2>

                    <p>

                        Passionate people building the future of
                        freelancing together.

                    </p>

                </div>

                <div className="team-grid">

                    {

                        members.map((member)=>(

                            <div
                                className="team-card"
                                key={member.id}
                            >

                                <div className="team-image">

                                    <img
                                        src={member.image}
                                        alt={member.name}
                                    />

                                </div>

                                <div className="team-content">

                                    <h3>

                                        {member.name}

                                    </h3>

                                    <span>

                                        {member.role}

                                    </span>

                                    <div className="team-social">

                                        <FaLinkedin size={18} />

                                        <FaGithub size={18} />

                                        <FaTwitter size={18} />

                                    </div>

                                </div>

                            </div>

                        ))

                    }

                </div>

            </div>

        </section>

    );

}

export default Team;
