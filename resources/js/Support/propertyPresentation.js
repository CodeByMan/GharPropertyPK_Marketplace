const propertyPlaceholderImage = '/images/gharproperty/property-placeholder.svg'
const propertyFallbackImages = Array.from(
  { length: 9 },
  (_, index) => `/images/gharproperty/properties/property-${String(index + 1).padStart(2, '0')}.webp`,
)

const imageSource = image => image?.src ?? image?.url ?? image?.path ?? null
const isLegacyDemoPlaceholder = source => typeof source === 'string'
  && source.includes('/demo/listings/')
  && /\.svg(?:$|\?)/i.test(source)

const stringHash = value => {
  let hash = 2166136261

  for (const character of String(value)) {
    hash ^= character.charCodeAt(0)
    hash = Math.imul(hash, 16777619)
  }

  return hash >>> 0
}

const listingSeed = listing => [
  listing?.id,
  listing?.title,
  listing?.city,
  listing?.street,
  listing?.street_nr,
  listing?.code,
]
  .filter(value => value !== null && value !== undefined && value !== '')
  .join('|') || 'gharproperty-default-listing'

const fallbackImageIndexes = (listing, count = 3) => {
  const totalImages = propertyFallbackImages.length
  const safeCount = Math.max(1, Math.min(count, totalImages))
  const seed = stringHash(listingSeed(listing))
  const start = seed % totalImages
  const steps = [1, 2, 4, 5, 7, 8]
  const step = steps[seed % steps.length]

  return Array.from({ length: safeCount }, (_, offset) => (start + (offset * step)) % totalImages)
}

export const listingFallback = (listing, offset = 0) => {
  const indexes = fallbackImageIndexes(listing, propertyFallbackImages.length)
  const safeOffset = Math.abs(Number(offset) || 0) % indexes.length

  return propertyFallbackImages[indexes[safeOffset]] ?? propertyPlaceholderImage
}

export const listingCover = listing => {
  const dedicatedImage = listing?.images
    ?.map(imageSource)
    .find(source => Boolean(source) && !isLegacyDemoPlaceholder(source))

  return dedicatedImage ?? listingFallback(listing)
}

export const listingGallery = listing => {
  const dedicatedImages = (listing?.images ?? [])
    .map((image, index) => ({
      id: image.id ?? `listing-image-${index}`,
      src: imageSource(image),
      isFallback: false,
    }))
    .filter(image => Boolean(image.src) && !isLegacyDemoPlaceholder(image.src))

  if (dedicatedImages.length > 0) return dedicatedImages

  return fallbackImageIndexes(listing, 3).map((imageIndex, index) => ({
    id: `packaged-property-${imageIndex + 1}-${index}`,
    src: propertyFallbackImages[imageIndex],
    isFallback: true,
  }))
}

export const propertyType = listing => {
  const explicitType = listing?.property_type ?? listing?.type

  if (explicitType) return explicitType

  const searchableAddress = [listing?.street_nr, listing?.street]
    .filter(Boolean)
    .join(' ')
    .toLowerCase()

  if (searchableAddress.includes('apartment') || searchableAddress.includes('flat')) return 'Apartment'
  if (searchableAddress.includes('villa')) return 'Villa'
  if (searchableAddress.includes('farmhouse') || searchableAddress.includes('farm house')) return 'Farmhouse'
  if (searchableAddress.includes('office') || searchableAddress.includes('shop') || searchableAddress.includes('commercial')) return 'Commercial'

  return 'House'
}

export const propertyTitle = listing => {
  if (listing?.title) return listing.title

  return `${listing?.beds ?? 0}-bed ${propertyType(listing).toLowerCase()} in ${listing?.city ?? 'Pakistan'}`
}

export const relativePostedTime = listing => {
  if (listing?.posted_at_human) return listing.posted_at_human
  if (!listing?.created_at) return 'Recently posted'

  const createdAt = new Date(listing.created_at)
  if (Number.isNaN(createdAt.getTime())) return 'Recently posted'

  const differenceInSeconds = Math.max(0, Math.floor((Date.now() - createdAt.getTime()) / 1000))

  if (differenceInSeconds < 60) return 'Just now'

  const minutes = Math.floor(differenceInSeconds / 60)
  if (minutes < 60) return `${minutes} minute${minutes === 1 ? '' : 's'} ago`

  const hours = Math.floor(minutes / 60)
  if (hours < 24) return `${hours} hour${hours === 1 ? '' : 's'} ago`

  const days = Math.floor(hours / 24)
  if (days < 7) return `${days} day${days === 1 ? '' : 's'} ago`

  const weeks = Math.floor(days / 7)
  if (days < 30) return `${weeks} week${weeks === 1 ? '' : 's'} ago`

  const months = Math.floor(days / 30)
  if (days < 365) return `${months} month${months === 1 ? '' : 's'} ago`

  const years = Math.floor(days / 365)
  return `${years} year${years === 1 ? '' : 's'} ago`
}

export { propertyFallbackImages, propertyPlaceholderImage }
