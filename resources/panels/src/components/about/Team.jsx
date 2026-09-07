import { useEffect, useState } from "react";
import frontendApi from "../../shared/frontendApi";

const avatarColors = ["linear-gradient(135deg,#4F46E5,#7C3AED)", "linear-gradient(135deg,#06B6D4,#4F46E5)", "linear-gradient(135deg,#EC4899,#7C3AED)"];

const FALLBACK_TEAM = [
    { id: 1, name: "Azim Khan", role: "Founder of SkillNest", tagline: "Brand Partner with Amtech", image: null },
    { id: 2, name: "Nadeem Mansuri", role: "Co-Founder of SkillNest", tagline: null, image: null },
    { id: 3, name: "Sayed Mujeeb", role: "Brand Partner with SkillNest", tagline: "Director of Amtech", image: null },
];

function initials(name) {
    return (name || "K")
        .split(" ")
        .filter(Boolean)
        .slice(0, 2)
        .map((w) => w.charAt(0).toUpperCase())
        .join("");
}

function Team() {
    const [members, setMembers] = useState([]);

    useEffect(() => {
        let active = true;
        frontendApi
            .get("/frontend/team")
            .then((res) => {
                const data = Array.isArray(res.data) ? res.data : [];
                if (!active) return;
                if (data.length > 0) {
                    setMembers(data.map((m) => ({
                        id: m.id,
                        name: m.name,
                        role: m.role,
                        tagline: m.tagline,
                        image: m.image_url || m.image || null,
                    })));
                } else {
                    setMembers([]);
                }
            })
            .catch((err) => {
                console.error("Team API fetch failed, using fallback team:", err);
                if (active) setMembers(FALLBACK_TEAM);
            });
        return () => { active = false; };
    }, []);

    if (members.length === 0) return null;

    return (
        <section className="about-team">
            <div className="container">
                <div className="section-heading">
                    <span className="section-tag">OUR TEAM</span>
                    <h2>Meet Our Awesome Team</h2>
                    <p>The people behind SkillNest building the future of freelancing.</p>
                </div>

                <div className="team-grid">
                    {members.map((member, index) => (
                        <div className="team-card" key={member.id}>
                            <div className="team-image">
                                {member.image ? (
                                    <img src={member.image} alt={member.name} />
                                ) : (
                                    <div
                                        className="team-avatar"
                                        style={{ background: avatarColors[index % avatarColors.length] }}
                                    >
                                        {initials(member.name)}
                                    </div>
                                )}
                            </div>
                            <div className="team-content">
                                <h3>{member.name}</h3>
                                <span>{member.role}</span>
                                {member.tagline && <p className="team-tagline">{member.tagline}</p>}
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}

export default Team;