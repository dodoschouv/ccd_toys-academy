const COOKIE_NAME = 'toys_academy_email'
const MAX_AGE_DAYS = 365

export function setSubscriberEmail(email) {
  if (!email || typeof email !== 'string') return
  const value = encodeURIComponent(email.trim())
  const maxAge = MAX_AGE_DAYS * 24 * 60 * 60
  document.cookie = `${COOKIE_NAME}=${value}; max-age=${maxAge}; path=/; SameSite=Lax`
}

export function getSubscriberEmail() {
  const match = document.cookie.match(new RegExp(`(?:^|;\\s*)${COOKIE_NAME}=([^;]*)`))
  if (!match) return null
  try {
    return decodeURIComponent(match[1]).trim() || null
  } catch {
    return null
  }
}

export function clearSubscriberEmail() {
  document.cookie = `${COOKIE_NAME}=; max-age=0; path=/`
}
