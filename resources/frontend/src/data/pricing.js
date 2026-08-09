const pricing = [
    {
        id: 1,
        name: "Starter",
        price: 0,
        duration: "Forever",
        description: "Perfect for freelancers getting started.",
        popular: false,
        features: [
            "Create Profile",
            "Browse Projects",
            "Basic Support",
            "5 Proposals / Month",
            "Community Access",
        ],
    },
    {
        id: 2,
        name: "Professional",
        price: 19,
        duration: "/month",
        description: "Best choice for growing freelancers.",
        popular: true,
        features: [
            "Unlimited Proposals",
            "Priority Support",
            "Featured Profile",
            "Advanced Analytics",
            "Project Alerts",
        ],
    },
    {
        id: 3,
        name: "Enterprise",
        price: 49,
        duration: "/month",
        description: "Perfect for agencies and large businesses.",
        popular: false,
        features: [
            "Team Members",
            "Dedicated Manager",
            "Unlimited Projects",
            "Premium Analytics",
            "24/7 Support",
        ],
    },
];

export default pricing;
