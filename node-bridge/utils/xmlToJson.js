const parser = require('xml2json');

const convert = async xml => {
    let response = {
        success: false,
        cod_error: 500,
        message_error: 'Error inesperado.',
        data: []
    }

    try {
        const options = {
            object: true,
            reversible: true,
            coerce: false,
            sanitize: false,
            trim: true,
            arrayNotation: false,
            alternateTextNode: true
        };
        const data = await parser.toJson(xml, options);
        const dataResponse = await data.soapEnvelope.soapBody.response;
        response = {
            success: !!dataResponse?.success?._t,
            cod_error: dataResponse?.cod_error?._t,
            message_error: dataResponse?.message_error?._t,
            data: dataResponse?.data?._t || [],
        }
    } catch (e) {
        console.log(e)
    }

    return response;
}

module.exports = {
    convert
}