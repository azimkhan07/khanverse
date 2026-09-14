import { useEffect, useState } from "react";
import frontendApi from "../frontendApi";
import "./LocationFields.css";

function parseList(data) {
    const list = data && data.data ? data.data : data || [];
    return Array.isArray(list) ? list : [];
}

function withFallback(list, selectedId, fallbackLabel) {
    if (!selectedId) return list;
    if (list.some((item) => String(item.id) === String(selectedId))) return list;
    if (!fallbackLabel) return list;
    return [{ id: Number(selectedId), name: fallbackLabel }, ...list];
}

function LocationFields({ value = {}, onChange = () => {}, labels = {}, required = false, style }) {
    const countryId = value.country_id || null;
    const stateId = value.state_id || null;
    const cityId = value.city_id || null;

    const [countries, setCountries] = useState([]);
    const [states, setStates] = useState([]);
    const [cities, setCities] = useState([]);
    const [loadingCountries, setLoadingCountries] = useState(true);
    const [statesLoaded, setStatesLoaded] = useState(false);
    const [citiesLoaded, setCitiesLoaded] = useState(false);

    useEffect(() => {
        let active = true;
        frontendApi
            .get("/frontend/countries")
            .then(({ data }) => { if (active) setCountries(parseList(data)); })
            .catch(() => {})
            .finally(() => { if (active) setLoadingCountries(false); });
        return () => { active = false; };
    }, []);

    useEffect(() => {
        if (!countryId) return;
        let active = true;
        frontendApi
            .get(`/frontend/states/${countryId}`)
            .then(({ data }) => { if (active) setStates(parseList(data)); })
            .catch(() => { if (active) setStates([]); })
            .finally(() => { if (active) setStatesLoaded(true); });
        return () => { active = false; };
    }, [countryId]);

    useEffect(() => {
        if (!stateId) return;
        let active = true;
        frontendApi
            .get(`/frontend/cities/${stateId}`)
            .then(({ data }) => { if (active) setCities(parseList(data)); })
            .catch(() => { if (active) setCities([]); })
            .finally(() => { if (active) setCitiesLoaded(true); });
        return () => { active = false; };
    }, [stateId]);

    const statesLoading = !!countryId && !statesLoaded;
    const citiesLoading = !!stateId && !citiesLoaded;

    const set = (patch) => onChange({ ...(value || {}), ...patch });

    const onCountry = (e) => {
        const id = e.target.value ? Number(e.target.value) : null;
        setStates([]);
        setCities([]);
        setStatesLoaded(false);
        setCitiesLoaded(false);
        set({ country_id: id, state_id: null, city_id: null });
    };

    const onState = (e) => {
        const id = e.target.value ? Number(e.target.value) : null;
        setCities([]);
        setCitiesLoaded(false);
        set({ state_id: id, city_id: null });
    };

    const onCity = (e) => set({ city_id: e.target.value ? Number(e.target.value) : null });

    return (
        <div className="lf-grid" style={style}>
            <div className="lf-field">
                <label>Country{required ? " *" : ""}</label>
                <select className="lf-select" value={countryId ?? ""} onChange={onCountry} disabled={loadingCountries}>
                    <option value="">{loadingCountries ? "Loading countries…" : "Select country"}</option>
                    {countries.map((c) => (
                        <option key={c.id} value={c.id}>{c.emoji ? `${c.emoji} ` : ""}{c.name}</option>
                    ))}
                </select>
            </div>
            <div className="lf-field">
                <label>State{required ? " *" : ""}</label>
                <select className="lf-select" value={stateId ?? ""} onChange={onState} disabled={!countryId || statesLoading}>
                    <option value="">
                        {!countryId ? "Select country first" : statesLoading ? "Loading states…" : "Select state"}
                    </option>
                    {withFallback(states, stateId, labels.state).map((s) => (
                        <option key={s.id} value={s.id}>{s.name}</option>
                    ))}
                </select>
            </div>
            <div className="lf-field">
                <label>City{required ? " *" : ""}</label>
                <select className="lf-select" value={cityId ?? ""} onChange={onCity} disabled={!stateId || citiesLoading}>
                    <option value="">
                        {!stateId ? "Select state first" : citiesLoading ? "Loading cities…" : "Select city"}
                    </option>
                    {withFallback(cities, cityId, labels.city).map((c) => (
                        <option key={c.id} value={c.id}>{c.name}</option>
                    ))}
                </select>
            </div>
        </div>
    );
}

export default LocationFields;