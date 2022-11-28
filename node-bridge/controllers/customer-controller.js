const { callSoapServer } = require("../adapters/soap-server")

const registerCustomer = async (req, res, next) => {
    const path = 'api/customer';
    const method = 'POST'
    const tag = 'customer';
    const body = req.body;
    const response = await callSoapServer(path, method, tag, body)
    res.json(response);
};

module.exports = {
    registerCustomer
}
