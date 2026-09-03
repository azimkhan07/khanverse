import { motion } from "framer-motion";
import team1 from "../../theme/images/2.jpg";
import team2 from "../../theme/images/4.jpg";
import team3 from "../../theme/images/2.jpg";
import team4 from "../../theme/images/4.jpg";

import { FaGithub, FaLinkedin, FaTwitter } from "react-icons/fa";

const gridVariants = {
    hidden: {},
    visible: {
        transition: {
            staggerChildren: 0.1,
        },
    },
};

const cardVariants = {
    hidden: { opacity: 0, y: 30 },
    visible: {
        opacity: 1,
        y: 0,
        transition: { duration: 0.6, ease: [0.22, 1, 0.36, 1] },
    },
};

function Team() {
    const members = [
        {
            id: 1,
            image: team1,
            name: "Azim Khan",
            role: "Founder & CEO",
            socials: [
                { icon: FaLinkedin, url: "#" },
                { icon: FaGithub, url: "#" },
                { icon: FaTwitter, url: "#" },
            ],
        },
        {
            id: 2,
            image: team2,
            name: "Sarah Wilson",
            role: "UI / UX Designer",
            socials: [
                { icon: FaLinkedin, url: "#" },
                { icon: FaGithub, url: "#" },
                { icon: FaTwitter, url: "#" },
            ],
        },
        {
            id: 3,
            image: team3,
            name: "Michael James",
            role: "Backend Developer",
            socials: [
                { icon: FaLinkedin, url: "#" },
                { icon: FaGithub, url: "#" },
                { icon: FaTwitter, url: "#" },
            ],
        },
        {
            id: 4,
            image: team4,
            name: "Emily Brown",
            role: "Support Manager",
            socials: [
                { icon: FaLinkedin, url: "#" },
                { icon: FaGithub, url: "#" },
                { icon: FaTwitter, url: "#" },
            ],
        },
    ];

    return (
        <section className="about-team">
            <div className="container">
                <div className="section-heading">
                    <span className="section-tag">OUR TEAM</span>
                    <h2>Meet Our Amazing Team</h2>
                    <p>Passionate people building the future of freelancing together.</p>
                </div>

                <motion.div
                    className="team-grid"
                    variants={gridVariants}
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: "-60px" }}
                >
                    {members.map((member) => (
                        <motion.div
                            className="team-card"
                            key={member.id}
                            variants={cardVariants}
                            whileHover={{ y: -6, scale: 1.02, transition: { duration: 0.3 } }}
                        >
                            <div className="team-image">
                                <img src={member.image} alt={member.name} />
                            </div>
                            <div className="team-content">
                                <h3>{member.name}</h3>
                                <span>{member.role}</span>
                                <div className="team-social">
                                    {member.socials.map((s, i) => (
                                        <a href={s.url} key={i} target="_blank" rel="noopener noreferrer">
                                            <s.icon size={18} />
                                        </a>
                                    ))}
                                </div>
                            </div>
                        </motion.div>
                    ))}
                </motion.div>
            </div>
        </section>
    );
}

export default Team;
