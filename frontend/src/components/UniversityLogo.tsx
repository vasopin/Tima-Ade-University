import Link from 'next/link'

interface UniversityLogoProps {
  size?: 'sm' | 'md' | 'lg'
  showText?: boolean
  href?: string
  className?: string
  imageClassName?: string
  onClick?: () => void
}

const sizeMap = {
  sm: 'h-8 w-8',
  md: 'h-12 w-12',
  lg: 'h-14 w-14',
}

export default function UniversityLogo({
  size = 'md',
  showText = false,
  href = '/',
  className = '',
  imageClassName = '',
  onClick,
}: UniversityLogoProps) {
  const sizeClass = sizeMap[size]
  const finalImageClass = imageClassName || `${sizeClass} shrink-0 object-contain`

  const logoContent = (
    <>
      <img
        src="/images/tima-ade-university-logo.png"
        alt="Tima-Ade University logo"
        className={finalImageClass}
      />
      {showText && (
        <div>
          <div className="font-semibold tracking-[-0.03em]">Tima-Ade University</div>
          <div className="text-xs text-slate-400">Gabiley, Somaliland</div>
        </div>
      )}
    </>
  )

  if (href) {
    return (
      <Link
        href={href}
        className={`flex items-center gap-3 ${className}`}
        onClick={onClick}
        aria-label="Tima-Ade University home"
      >
        {logoContent}
      </Link>
    )
  }

  return <div className={`flex items-center gap-3 ${className}`}>{logoContent}</div>
}
