const maxHumidity = 1600;

export const calculateHumidity = (rawsValue) => {
    return (rawsValue / maxHumidity) * 100;
};

export const calculateFlowRate = (waterLv) => {
    return true
};
