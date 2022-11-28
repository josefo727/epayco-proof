const { callSoapServer } = require("../adapters/soap-server")

const chargeBalance = async (req, res, next) => {
    const path = 'api/transaction/charge-balance';
    const method = 'POST'
    const tag = 'transaction';
    const body = req.body;
    const response = await callSoapServer(path, method, tag, body)
    res.json(response);
};

const checkBalance = async (req, res, next) => {
    const path = 'api/wallet/check-balance';
    const method = 'POST'
    const tag = 'check-balance';
    const body = req.body;
    const response = await callSoapServer(path, method, tag, body)
    res.json(response);
};

const requestPayment = async (req, res, next) => {
    const path = 'api/transaction/request-payment';
    const method = 'POST'
    const tag = 'request-payment';
    const body = req.body;
    const response = await callSoapServer(path, method, tag, body)
    res.json(response);
};

const confirmPayment = async (req, res, next) => {
    const path = 'api/transaction/confirm-payment';
    const method = 'POST'
    const tag = 'confirm-payment';
    const body = req.body;
    const response = await callSoapServer(path, method, tag, body)
    res.json(response);
};

module.exports = {
    chargeBalance,
    checkBalance,
    requestPayment,
    confirmPayment
}
