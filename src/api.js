export async function apiRequest(url, options) {
  const response = await fetch(url, options);
  const text = await response.text();
  let body;

  try {
    body = text ? JSON.parse(text) : null;
  } catch {
    throw new Error(`Server returned an invalid response (${response.status}).`);
  }

  if (!response.ok) {
    throw new Error(`Request failed (${response.status}).`);
  }

  if (!body || typeof body !== 'object' || Array.isArray(body)) {
    throw new Error(`Server returned an unexpected response (${response.status}).`);
  }

  return body;
}
