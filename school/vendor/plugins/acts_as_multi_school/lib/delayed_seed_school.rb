class DelayedSeedSchool
  def perform
    system("rake champs21:seed_schools")
  end
end