const xmlBody = (tag, body) => {
    return `
    <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
      <soap:Body>
        <${tag} xmlns="http://www.dataaccess.com/webservicesserver/">
          ${body}
        </${tag}>
      </soap:Body>
    </soap:Envelope>
    `
}

const buildData = data => {
    let body = '';
    const keys = Object.keys(data);
    keys.forEach((key, index) => {
        body += `<${key}>${data[key]}</${key}>${keys.length - 1 !== index ? '\n' : null}`;
    });
    return body;
}

const getXmlContent = (tag, data) => {
    const body = buildData(data);
    return xmlBody(tag, body);
}

module.exports = {
    getXmlContent
}