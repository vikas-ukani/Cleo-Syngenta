
export const __uniqueByNameKey = (arrayData) => {
    let newArrayData = [];
    arrayData.forEach(data => {
        if (data && data.name && newArrayData.filter(nArrayData => (nArrayData && nArrayData.name && nArrayData.name == data.name)).length == 0) {
            newArrayData.push(data)
        }
    });
    return newArrayData
}