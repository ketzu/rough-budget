const textEncoder = new TextEncoder();

export const PROTOCOL_VERSION = 2;
export const KDF = "PBKDF2-SHA-256";
export const KDF_ITERATIONS = 600000;
export const SALT_BYTES = 16;
export const PROTOCOLS = Object.freeze({
  2: Object.freeze({
    kdf: KDF,
    iterations: KDF_ITERATIONS,
    encryptionInfo: "rough-budget encryption v2",
    authenticationInfo: "rough-budget authentication v2"
  })
});

const toBase64 = bytes => btoa(String.fromCharCode(...new Uint8Array(bytes)));

export const fromBase64 = value => Uint8Array.from(atob(value), char => char.charCodeAt(0));

const hkdf = async (master, label) => {
  const key = await window.crypto.subtle.importKey("raw", master, "HKDF", false, ["deriveBits"]);
  const bits = await window.crypto.subtle.deriveBits({
    name: "HKDF",
    hash: "SHA-256",
    salt: new Uint8Array(32),
    info: textEncoder.encode(label)
  }, key, 256);
  return bits;
};

export const deriveKeys = async (password, salt, version = PROTOCOL_VERSION) => {
  const protocol = PROTOCOLS[version];
  if (!protocol) throw new Error(`Unsupported security protocol: ${version}`);
  const passwordKey = await window.crypto.subtle.importKey(
    "raw",
    textEncoder.encode(password),
    "PBKDF2",
    false,
    ["deriveBits"]
  );
  const master = await window.crypto.subtle.deriveBits({
    name: "PBKDF2",
    salt,
    iterations: protocol.iterations,
    hash: "SHA-256"
  }, passwordKey, 256);
  const encryptionBits = await hkdf(master, protocol.encryptionInfo);
  const authenticationBits = await hkdf(master, protocol.authenticationInfo);
  const encryptionKey = await window.crypto.subtle.importKey(
    "raw",
    encryptionBits,
    { name: "AES-GCM" },
    false,
    ["encrypt", "decrypt"]
  );

  return {
    encryptionKey,
    authenticationKey: toBase64(authenticationBits)
  };
};

// --- Legacy Part-- -

const deriveLegacyKey = async (password, iterations, extractable) => {
  const passwordKey = await window.crypto.subtle.importKey(
    "raw",
    textEncoder.encode("my password"),
    "PBKDF2",
    false,
    ["deriveKey"]
  );
  const key = await window.crypto.subtle.deriveKey({
    name: "PBKDF2",
    salt: textEncoder.encode(password),
    iterations,
    hash: "SHA-256"
  }, passwordKey, { name: "AES-GCM", length: 256 }, extractable, ["encrypt", "decrypt"]);
  if (!extractable) return key;
  const jwk = await window.crypto.subtle.exportKey("jwk", key);
  return jwk.k;
};

export const deriveLegacyAuthenticationKey = password => deriveLegacyKey(password, 5000, true);
export const deriveLegacyEncryptionKey = password => deriveLegacyKey(password, 2500, false);