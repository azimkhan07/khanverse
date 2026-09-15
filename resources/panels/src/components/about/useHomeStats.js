import useSiteSections from "../../hooks/useSiteSections";

export default function useHomeStats() {
    const { stats, sections } = useSiteSections();
    return { stats, sections, loading: false };
}