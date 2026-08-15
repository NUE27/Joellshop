// LZ Pedia serverless proxy alternative.
// Configure LZPEDIA_API_KEY in the deployment environment; never place it in browser code.
export default async function handler(req, res) {
    res.setHeader('Content-Type', 'application/json; charset=utf-8');
    res.setHeader('Vary', 'Origin');
    if (req.method === 'OPTIONS') return res.status(204).end();

    const { action, amount, invoice_id } = req.query || {};
    const apiKey = process.env.LZPEDIA_API_KEY;
    const baseUrl = (process.env.LZPEDIA_BASE_URL || 'https://app.lzpedia.my.id/api').replace(/\/$/, '');
    if (!apiKey) return res.status(500).json({ success: false, error: 'LZPEDIA_API_KEY belum dikonfigurasi di server' });

    let endpoint;
    if (action === 'create') {
        const numericAmount = Number.parseInt(amount, 10);
        if (!Number.isInteger(numericAmount) || numericAmount <= 0) return res.status(400).json({ success: false, error: 'Amount harus berupa bilangan bulat lebih dari 0' });
        endpoint = `${baseUrl}/invoice?${new URLSearchParams({ apikey: apiKey, amount: String(numericAmount) })}`;
    } else if (action === 'status') {
        if (!invoice_id) return res.status(400).json({ success: false, error: 'invoice_id wajib diisi' });
        endpoint = `${baseUrl}/invoice/status?${new URLSearchParams({ apikey: apiKey, invoice_id: String(invoice_id) })}`;
    } else {
        return res.status(400).json({ success: false, error: 'Action tidak valid' });
    }

    try {
        const response = await fetch(endpoint, { headers: { Accept: 'application/json', 'User-Agent': 'JOELL-SHOP/3.0' } });
        const data = await response.json();
        return res.status(response.ok ? 200 : response.status).json(response.ok ? data : { success: false, error: `Gateway HTTP ${response.status}`, details: data });
    } catch (error) {
        return res.status(502).json({ success: false, error: 'Gateway pembayaran tidak dapat dihubungi' });
    }
}
