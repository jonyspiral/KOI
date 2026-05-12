#!/usr/bin/env node
/**
 * Reporte anual de movimientos Mercado Pago 2025
 * Proyecto: cumbresymareas-web (koi2)
 *
 * Uso:
 *   MERCADOPAGO_ACCESS_TOKEN=APP_USR-... node tools/mp-report-2025.mjs
 *   MERCADOPAGO_ACCESS_TOKEN=... node tools/mp-report-2025.mjs --year=2024
 *   MERCADOPAGO_ACCESS_TOKEN=... node tools/mp-report-2025.mjs --json
 *
 * Requiere Node.js >= 18 (fetch nativo).
 */

const ACCESS_TOKEN = process.env.MERCADOPAGO_ACCESS_TOKEN;
const BASE_URL = 'https://api.mercadopago.com';

// Parsear args
const args = Object.fromEntries(
  process.argv.slice(2)
    .filter(a => a.startsWith('--'))
    .map(a => { const [k, v] = a.slice(2).split('='); return [k, v ?? true]; })
);
const YEAR = parseInt(args.year ?? '2025');
const OUTPUT_JSON = args.json === true;

if (!ACCESS_TOKEN) {
  console.error('ERROR: Falta MERCADOPAGO_ACCESS_TOKEN');
  console.error('Uso: MERCADOPAGO_ACCESS_TOKEN=APP_USR-... node tools/mp-report-2025.mjs');
  process.exit(1);
}

async function mpGet(path) {
  const res = await fetch(`${BASE_URL}${path}`, {
    headers: { 'Authorization': `Bearer ${ACCESS_TOKEN}` }
  });
  if (!res.ok) {
    const text = await res.text();
    throw new Error(`MP API ${res.status}: ${text}`);
  }
  return res.json();
}

async function fetchAllPayments(year) {
  const beginDate = `${year}-01-01T00:00:00.000-03:00`;
  const endDate   = `${year}-12-31T23:59:59.999-03:00`;
  const limit = 50;
  let offset = 0;
  let total = null;
  const payments = [];

  process.stderr.write(`Buscando pagos ${year}...`);

  while (true) {
    const params = new URLSearchParams({
      begin_date: beginDate,
      end_date: endDate,
      sort: 'date_created',
      criteria: 'asc',
      limit: String(limit),
      offset: String(offset),
    });

    const data = await mpGet(`/v1/payments/search?${params}`);
    if (total === null) total = data.paging.total;

    payments.push(...data.results);
    process.stderr.write(` ${payments.length}/${total}`);

    if (payments.length >= total || data.results.length === 0) break;
    offset += limit;
  }

  process.stderr.write('\n');
  return payments;
}

function formatARS(n) {
  return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(n);
}

function formatUSD(n) {
  return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'USD' }).format(n);
}

function monthName(n) {
  return ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'][n];
}

function buildReport(payments, year) {
  const approved = payments.filter(p => p.status === 'approved');

  // Totales por moneda
  const totals = {};
  for (const p of approved) {
    const cur = p.currency_id || 'ARS';
    if (!totals[cur]) totals[cur] = { bruto: 0, neto: 0, comisiones: 0, count: 0 };
    totals[cur].bruto += p.transaction_amount ?? 0;
    totals[cur].neto  += p.transaction_details?.net_received_amount ?? 0;
    totals[cur].count++;
    const fees = (p.fee_details ?? []).reduce((s, f) => s + (f.amount ?? 0), 0);
    totals[cur].comisiones += fees;
  }

  // Por mes
  const byMonth = {};
  for (const p of approved) {
    const d = new Date(p.date_approved ?? p.date_created);
    const key = d.getMonth(); // 0-11
    const cur = p.currency_id || 'ARS';
    if (!byMonth[key]) byMonth[key] = {};
    if (!byMonth[key][cur]) byMonth[key][cur] = { bruto: 0, neto: 0, count: 0 };
    byMonth[key][cur].bruto += p.transaction_amount ?? 0;
    byMonth[key][cur].neto  += p.transaction_details?.net_received_amount ?? 0;
    byMonth[key][cur].count++;
  }

  // Por estado
  const byStatus = {};
  for (const p of payments) {
    byStatus[p.status] = (byStatus[p.status] ?? 0) + 1;
  }

  // Por método de pago
  const byMethod = {};
  for (const p of approved) {
    const method = p.payment_method_id ?? 'desconocido';
    byMethod[method] = (byMethod[method] ?? 0) + 1;
  }

  // Transacciones aprobadas (detalle)
  const transactions = approved.map(p => ({
    id: p.id,
    fecha: (p.date_approved ?? p.date_created ?? '').slice(0, 10),
    external_reference: p.external_reference,
    descripcion: p.description,
    moneda: p.currency_id,
    bruto: p.transaction_amount,
    neto: p.transaction_details?.net_received_amount,
    cuotas: p.installments,
    metodo: p.payment_method_id,
    status_detail: p.status_detail,
    pagador_email: p.payer?.email,
  }));

  return { year, totals, byMonth, byStatus, byMethod, transactions, totalPayments: payments.length };
}

function printTextReport(report) {
  const line = '─'.repeat(60);
  console.log(`\n${'═'.repeat(60)}`);
  console.log(`  REPORTE MERCADO PAGO — AÑO ${report.year}`);
  console.log(`  Proyecto: cumbresymareas.com.ar`);
  console.log(`${'═'.repeat(60)}\n`);

  console.log(`Total transacciones encontradas: ${report.totalPayments}`);

  console.log(`\n${line}`);
  console.log(' ESTADOS');
  console.log(line);
  for (const [status, count] of Object.entries(report.byStatus).sort((a,b) => b[1]-a[1])) {
    console.log(`  ${status.padEnd(20)} ${String(count).padStart(5)}`);
  }

  console.log(`\n${line}`);
  console.log(' TOTALES APROBADOS');
  console.log(line);
  for (const [cur, t] of Object.entries(report.totals)) {
    const fmt = cur === 'ARS' ? formatARS : formatUSD;
    console.log(`  ${cur}  Transacciones: ${t.count}`);
    console.log(`      Bruto cobrado:  ${fmt(t.bruto)}`);
    console.log(`      Comisiones MP:  ${fmt(t.comisiones)}`);
    console.log(`      Neto recibido:  ${fmt(t.neto)}`);
    console.log('');
  }

  console.log(`${line}`);
  console.log(' POR MES (aprobados)');
  console.log(line);
  for (let m = 0; m < 12; m++) {
    const data = report.byMonth[m];
    if (!data) continue;
    const parts = Object.entries(data).map(([cur, v]) => {
      const fmt = cur === 'ARS' ? formatARS : formatUSD;
      return `${cur} ${fmt(v.bruto)} (${v.count} pago${v.count !== 1 ? 's' : ''})`;
    });
    console.log(`  ${monthName(m)}  ${parts.join('  |  ')}`);
  }

  console.log(`\n${line}`);
  console.log(' MÉTODOS DE PAGO (aprobados)');
  console.log(line);
  for (const [method, count] of Object.entries(report.byMethod).sort((a,b) => b[1]-a[1])) {
    console.log(`  ${method.padEnd(30)} ${String(count).padStart(5)}`);
  }

  console.log(`\n${line}`);
  console.log(' DETALLE DE TRANSACCIONES APROBADAS');
  console.log(line);
  const hdr = 'Fecha       ID          Referencia              Moneda     Bruto          Neto           Cuotas  Método';
  console.log(hdr);
  console.log('─'.repeat(hdr.length));
  for (const t of report.transactions) {
    const ref = (t.external_reference ?? '').slice(0, 22).padEnd(22);
    const bruto = String(t.bruto?.toFixed(2) ?? '').padStart(14);
    const neto  = String(t.neto?.toFixed(2)  ?? '').padStart(14);
    console.log(`${t.fecha}  ${String(t.id).padEnd(10)}  ${ref}  ${(t.moneda??'').padEnd(6)} ${bruto}  ${neto}  ${String(t.cuotas??1).padStart(5)}x  ${t.metodo ?? ''}`);
  }

  console.log(`\n${'═'.repeat(60)}\n`);
}

(async () => {
  try {
    const payments = await fetchAllPayments(YEAR);
    const report = buildReport(payments, YEAR);

    if (OUTPUT_JSON) {
      console.log(JSON.stringify(report, null, 2));
    } else {
      printTextReport(report);
    }
  } catch (e) {
    console.error('Error:', e.message);
    process.exit(1);
  }
})();
